<?php

class CliHelpArgsSingle extends CliHelpArgs {

  /**
   * @param $argv
   * @param string|object Class or object
   */
  function __construct($argv, $class) {
    $this->oneClass = is_object($class) ? get_class($class) : $class;
    parent::__construct($argv);
  }

  public function prefix() {
    return 'sman';
  }

  protected function _runner() {
    return lcfirst($this->oneClass);
  }

}