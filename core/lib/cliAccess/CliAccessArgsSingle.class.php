<?php

class CliAccessArgsSingle extends CliAccessArgs {

  /**
   * @param $argParams
   * @param string|object $class Class or object
   */
  function __construct($argParams, $class) {
    $this->oneClass = is_object($class) ? get_class($class) : $class;
    parent::__construct($argParams);
  }

  function prefix() {
    return false;
  }

  protected function _runner() {
    return lcfirst($this->oneClass);
  }

}