<?php

class CliAccessArgsSingleProject extends CliAccessArgsSingle {

  function __construct($argParams, $class) {
    parent::__construct(explode(' ', $argParams), $class);
  }

  protected function _runner() {
    return 'dqwdwq';
  }

}