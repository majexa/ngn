<?php

class CliTestRunner extends CliHelpArgs {

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
        'class' => 'TestRunnerCasper',
        'name' => 'casper'
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
        'class' => 'TestCliCommon',
        'name' => 'c'
      ],
    ];
  }

  protected function _runner() {
    return 'tst';
  }

}