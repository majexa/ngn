<?php

class CliAccessArgsSingle extends CliAccessArgs {

  /**
   * @param $argv
   * @param string|object $class Class or object
   */
  function __construct($argv, $class) {
    $this->oneClass = is_object($class) ? get_class($class) : $class;
    parent::__construct($argv);
  }

  function prefix() {
    return false;
  }

  protected function _runner() {
    return lcfirst($this->oneClass);
  }

}