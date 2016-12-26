<?php

class CliAccessArgsSingle extends CliAccessArgs {

  /**
   * @param $argParams
   * @param string|object $class Class or object
   * @param array $options
   */
  function __construct($argParams, $class, array $options = []) {
    $this->oneClass = is_object($class) ? get_class($class) : $class;
    parent::__construct($argParams, $options);
  }

  function prefix() {
    return false;
  }

  protected function _runner() {
    if (isset($this->options['runner'])) {
      return $this->options['runner'];
    }
    return lcfirst($this->oneClass);
  }

}