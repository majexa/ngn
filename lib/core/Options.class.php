<?php

trait Options {

  static $requiredOptions = []; // structure

  public $options = [];

  function setOptions(array $options) {
    $this->defineOptions();
    $this->optionsDefined = true;
    $this->options = array_merge($this->options, $options);
    foreach (static::$requiredOptions as $k)
      if (!isset($this->options[$k]))
        throw new Exception('Class "'.get_class($this).'": option "'.$k.'" does not exists');
  }

  protected function defineOptions() {}

}
