<?php

class ConfigManagerForm extends Form {

  protected $configType, $configName, $configValues, $configStruct, $configFields, $configDefaultData, $alert, $noRootKeys = false;

  /**
   * @param string $type Тип конфигурации (vars/constants)
   * @param string $name Имя конфигурационной группы (admins/database/emails/...)
   */
  function __construct($type, $name) {
    $type = str_replace('vvv', 'vars', $type);
    $this->configType = $type;
    $this->configName = $name;
    $this->initStruct();
    if ($this->configType == 'vars') $this->configValues = Config::getVar($this->configName, true);
    else {
      //die2();
      foreach (array_keys($this->configStruct['fields']) as $constantName) {
        $this->configValues[$constantName] = constant($constantName);
      }
    }
    if (!$this->configValues) $this->configValues = [];
    if (count($this->configStruct['fields']) == 1 and Arr::first($this->configStruct['fields'])['type'] == 'fieldList') {
      $this->noRootKeys = Arr::firstKey($this->configStruct['fields']);
      $this->configValues[$this->configName] = $this->configValues;
    }
    if (!empty($this->configStruct['dependRequire'])) $this->dependRequire = $this->configStruct['dependRequire'];
    if (!empty($this->configStruct['visibilityConditions'])) {
      foreach ($this->configStruct['visibilityConditions'] as $cond) {
        $this->addVisibilityCondition($cond['headerName'], $cond['condFieldName'], $cond['cond']);
      }
    }
    parent::__construct(new Fields(Fields::keyAsName($this->configStruct['fields'])));
  }

  static function cmd($code) {
    $p = WEBROOT_PATH;
    return `php $p/cmd.php "$code"`;
  }

  protected function isDisabled($fieldName) {
    if ($this->configType == 'vars') return false;
    $curValue = $this->configValues[$fieldName];
    SiteConfig::replaceConstant($this->configName, $fieldName, 'changed');
    SiteConfig::replaceConstant($this->configName, $fieldName, $curValue);
  }

  protected function init() {
    if (count($this->configValues) == 1 and isset($this->configValues[$this->configName])) $this->configValues = $this->configValues[$this->configName];
    $this->setElementsData(!isset($this->configStruct['type']) ? $this->configValues : [$this->configName => $this->configValues]);
    if ($this->configType == 'constants') {
      $this->alert = 'Внимание! Изменение этих параметров может повлиять на работоспособность сайта';
    }
  }

  protected function structExists() {
    return !empty($this->configStruct) ? true : false;
  }

  protected function initStruct() {
    // Приведение типов. Т.е. пустой меняем на 'text'
    $structs = SiteConfig::getStruct($this->configType);
    if (!isset($structs[$this->configName])) throw new Exception('Structure "'.$this->configName.'" not exists');
    $struct = $structs[$this->configName];
    $struct['fields'] = isset($struct['type']) ? [$this->configName => $struct] : $this->getStructFields($struct['fields']);
    if (!empty($this->alert)) {
      $struct['fields'] = array_merge([
          'alert' => [
            'title' => $this->alert,
            'type'  => 'header'
          ]
        ], $struct['fields']);
    }
    unset($struct['type'], $struct['fieldsType']); // мусор
    $this->configStruct = $struct;
  }

  protected function getStructFields($fields) {
    foreach ($fields as &$vv) {
      if (!isset($vv['type'])) $vv['type'] = 'text';
      // Для типа "fieldSet" перебераем вложенные поля
      if ($vv['type'] == 'fieldSet') {
        foreach ($vv['fields'] as &$vvv) {
          if (!isset($vvv['type'])) $vvv['type'] = 'text';
        }
      }
    }
    return $fields;
  }

  protected function _update(array $data) {
    if (isset($this->configStruct['type']) and $data) $data = $data[$this->configName];
    if ($this->noRootKeys) $data = $data[$this->configName];
    if ($this->configType == 'vars') {
      SiteConfig::updateVar($this->configName, $data);
    }
    else {
      SiteConfig::replaceConstants($this->configName, $data);
    }
    $this->afterUpdate($data);
  }

  protected function afterUpdate(array $values) {
  }

}