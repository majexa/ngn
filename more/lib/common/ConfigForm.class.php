<?php

class ConfigForm extends Form {

  function __construct($configKey, $fields) {
    $this->configKey = $configKey;
    $this->defaultData = Config::getVar($this->configKey, true) ?: [];
    parent::__construct($fields);
  }

  protected function _update(array $data) {
    ProjectConfig::updateVar($this->configKey, $data, true);
  }

}
