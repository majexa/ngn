<?php

class CliTestRunner extends CliHelpDirectClasses {

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
        'class' => 'TestCliCommon',
        'name' => 'c'
      ],
    ];
  }

  protected function _runner() {
    return 'tst';
  }

}