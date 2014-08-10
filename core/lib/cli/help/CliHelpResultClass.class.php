<?php

class CliHelpResultClass {

  public $class, $name;

  /**
   * @param string $class Class
   * @param string $name Command name
   */
  function __construct($class, $name) {
    $this->class = $class;
    $this->name = $name;
  }

}