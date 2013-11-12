<?php

class ConfigForm extends Form {

  protected $configKey;

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
    SiteConfig::updateVar($this->configKey, $data[$this->configKey], true);
    Sflm::clearCache();
  }

}