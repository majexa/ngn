<?php

class CliTestRunner extends CliHelpDirectClasses {

  public function prefix() {
    return false;
  }

  public function getClasses() {
    return [
      [
        'class' => 'ProjectTestRunner',
        'name' => 'proj'
      ],
      [
        'class' => 'TestRunner',
        'name' => 'ngn'
      ],
    ];
  }

  protected function _runner() {
    return 'ngn-test';
  }

}