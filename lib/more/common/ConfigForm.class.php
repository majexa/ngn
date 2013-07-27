<?php

class ConfigForm extends Form {

  protected $configKey;

  function __construct($configKey, $fields) {
    $this->configKey = $configKey;
    $this->defaultData = Config::getVar($this->configKey, true) ?: [];
    parent::__construct($fields);
  }

  protected function _update(array $data) {
    SiteConfig::updateVar($this->configKey, $data, true);
    Sflm::clearCache();
  }

}