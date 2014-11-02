<?php

class CliAccessResultClass {

  public $class, $name;

  /**
   * @param string $class Class
   * @param string $name Command name
   */
  function __construct($class) {
    $this->class = $class;
  }

}