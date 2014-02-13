<?php

class CliTestRunner extends CliHelpDirectClassesArgs {

  protected function getClasses() {
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

  protected function runner() {
    return 'ngn-test';
  }

}