<?php

class CliAccessArgsSingleProject extends CliAccessArgsSingle {

  function __construct($argv, $class) {
    parent::__construct(explode(' ', $argv), $class);
  }

  protected function _runner() {
    return 'dqwdwq';
  }

}