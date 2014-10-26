<?php

class CliTestRunner extends CliAccessArgs {

  function prefix() {
    return false;
  }

  function getClasses() {
    return [
      [
        'class' => 'TestRunnerProject',
        'name' => 'proj'
      ],
      [
        'class' => 'TestRunnerNgn',
        'name' => 'ngn'
      ],
      [
        'class' => 'TestRunnerLib',
        'name' => 'lib'
      ],
      [
        'class' => 'TestRunnerPlib',
        'name' => 'plib'
      ],
      [
        'class' => 'TestCliCommon',
        'name' => 'c'
      ],
    ];
  }

  protected function _runner() {
    return 'tst';
  }

  protected function _run(CliAccessArgsArgs $args) {
    if (isset($args->params[2]) and $args->params[2] == 'debug') TestCore::$debug = true;
    parent::_run($args);
  }

}