<?php

/**
 * Аргументы, необсходимые для запуска комманды
 */
class CliAccessArgsArgs {

  public $class, $method, $params;

  function __construct($class, $method, $params) {
    $this->class = $class;
    $this->method = $method;
    $this->params = $params;
  }

}