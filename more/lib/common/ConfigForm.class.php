<?php

class ConfigForm extends Form {

  protected $configKey;
  public $firstLevelKey;

  function __construct($configKey, $fields) {
    $this->configKey = $configKey;
    $field['name'] = $this->configKey;
    $field['type'] = 'fieldSet';
    $field['fields'] = $fields;
    $this->defaultData[$this->configKey] = Config::getVar($this->configKey, true) ? : [];
    parent::__construct([$field]);
    UploadTemp::extendFormOptions($this);
  }

  protected function _update(array $data) {
    $data = $data[$this->configKey];
    if (isset($this->firstLevelKey)) $data = Arr::assoc($data, $this->firstLevelKey);
    ProjectConfig::updateVar($this->configKey, $data, true);
    Sflm::clearCache();
  }

}