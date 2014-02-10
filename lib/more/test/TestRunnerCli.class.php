<?php

class TestRunnerCli extends CliHelpDirectClassesArgs {

  protected function getClasses() {
    return [
      'asd' => 'TestRunner',
      'asdd' => 'ProjectTestRunner'
    ];
  }

  protected function runner() {
    return 'test';
  }

}