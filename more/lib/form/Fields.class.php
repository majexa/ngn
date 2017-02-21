<?php

class Fields extends ArrayAccesseble {
  use Options;

  protected function &getArrayRef() {
    return $this->fields;
  }

  /**
   * Пример массива:
   * array(
   *   'name' => 'fieldName',
   *   'title' => 'Название поля',
   *   'type' => 'select',
   *   'descr' => 'Это поле - совсем не поле, а лужайка непаханая',
   *   'maxlength' => 255,
   *   'required' => 1,
   *   'options' => array(
   *     1, 2, 3
   *   )
   * )
   *
   */
  public $fields = [];

  protected $n = 1;

  function __construct(array $fields = [], array $options = []) {
    $this->setOptions($options);
    $this->init();
    $this->addFields($fields);
  }

  protected function init() {
  }

  protected function defineOptions() {
    return [
      'errorOnTypeNotExists' => false,
      'forceShow'     => [],
      'getHidden'     => false,
      'getSystem'     => false,
      'getDisallowed' => false,
      'getVirtual'    => false
    ];
  }

  function addFields(array $fields) {
    foreach ($fields as $v) $this->addField($v);
  }

  function getFields() {
    return $this->fields;
  }

  /**
   * Возвращает отфильтрованые поля
   *
   * @return array
   */
  function getFieldsF() {
    $fields = [];
    foreach ($this->getFields() as $f) {
      if (!in_array($f['name'], $this->options['forceShow'])) {
        if (!empty($f['disableUpdate'])) continue;
        if (!empty($f['system']) and !$this->options['getSystem']) continue;
        if (!empty($f['defaultDisallow']) and !$this->options['getDisallowed']) continue;
        if (!empty($f['virtual']) and !$this->options['getVirtual']) continue;
      }
      $fields[$f['name']] = $f;
    }
    //if (!$this->options['getSystem']) $fields = Arr::filterByValue($fields, 'system', 0, true);
    //if (!$this->options['getDisallowed']) $fields = Arr::filterByValue($fields, 'defaultDisallow', 0, true);
    //if (!$this->options['getVirtual']) $fields = Arr::filterByValue($fields, 'virtual', 0, true, true);
    //foreach ($fields as &$v) $v = Arr::filterEmptyStrings($v);
    return $fields;
  }

  function getInputFields() {
    return Arr::filterFunc($this->fields, function ($v) {
      return FieldCore::isInput($v['type']);
    });
  }

  function getType($name) {
    return isset($this->fields[$name]['type']) ? $this->fields[$name]['type'] : false;
  }

  function getTypes() {
    return Arr::get($this->getFields(), 'type', 'name');
  }

  /**
   * Возвращает только обязательные для заполнения поля
   *
   * @return array Массив с данными полей
   */
  function getRequired() {
    $fields = [];
    foreach ($this->getFields() as $k => $v) {
      if (!empty($v['required'])) {
        $fields[$k] = $v;
      }
    }
    return $fields;
  }

  function getTitle($name) {
    return $this->fields[$name]['title'];
  }

  function getField($name) {
    return $this->fields[$name];
  }

  function addField(array $v, $after = false) {
    if (!isset($v['name'])) $v['name'] = 'fld'.$this->n;
    if (!isset($v['type'])) $v['type'] = 'text';
    if (isset($this->fields[$v['name']])) unset($this->fields[$v['name']]);
    if ($after) $this->fields = Arr::injectAfter($this->fields, $after, [$v], 'name', true);
    else $this->fields[$v['name']] = $v;
    $this->n++;
  }

  function replaceField(array $field) {
    Arr::checkEmpty($field, 'name');
    foreach ($field as $k => $v) $this->fields[$field['name']][$k] = $v;
  }

  function getFieldsByAncestor($ancestorType) {
    $fields = [];
    foreach ($this->getFields() as $k => $v) {
      if (FieldCore::hasAncestor($v['type'], $ancestorType)) $fields[$k] = $v;
    }
    return $fields;
  }

  function getFileFields() {
    return $this->getFieldsByAncestor('file');
  }

  function getDateFields() {
    return $this->getFieldsByAncestor('date');
  }

  function exists($name) {
    return isset($this->fields[$name]);
  }

  function isFileType($name) {
    return $this->hasAncestor($name, 'file');
  }

  function hasAncestor($name, $ancestorType) {
    Misc::checkEmpty($name, 'WHY? :(');
    return FieldCore::hasAncestor($this->fields[$name]['type'], $ancestorType);
  }

  static function keyAsName(array $fields) {
    foreach ($fields as $k => &$v) $v['name'] = $k;
    return $fields;
  }

  static function defaults(array $types) {
    $r = [];
    foreach ($types as $v) {
      $class = 'FieldE'.ucfirst($v);
      $r[] = [
        'title' => $class::$title,
        'type'  => $v,
        'name'  => $v
      ];
    }
    return $r;
  }

  /**
   * @var Form
  public $form;

  function __set($k, $v) {
    $this->$k = $v;
    prr([$k, $v]);
  }
   */

}
