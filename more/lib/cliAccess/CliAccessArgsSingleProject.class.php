<?php

class CliAccessArgsSingleProject extends CliAccessArgsSingle {

  protected $rootRunner;

  function __construct($argParams, $class, $rootRunner) {
    $this->rootRunner = $rootRunner;
    parent::__construct(explode(' ', $argParams), $class);
  }

  protected function _runner() {
    return $this->rootRunner.' '.$this->initArgv[0];
  }

}