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
    ];
  }

  protected function _runner() {
    return 'ngn-test';
  }

}