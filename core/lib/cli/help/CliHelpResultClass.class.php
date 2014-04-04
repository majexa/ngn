<?php

class CliHelpResultClass {

  public $class, $name;

  /**
   * @param string Class
   * @param string Command name
   */
  function __construct($class, $name) {
    $this->class = $class;
    $this->name = $name;
  }

}