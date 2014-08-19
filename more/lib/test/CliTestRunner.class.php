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

  static function detectError($text) {
    if (strstr($text, 'Uncaught exception')) return '"Uncaught exception" in test result';
    elseif (strstr($text, 'failed')) return '"failed" in test result';
    elseif (strstr($text, 'FAILURES!')) return '"FAILURES!" in test result';
    elseif (strstr($text, 'Fatal error')) return '"Fatal error" in test result';
    return false;
  }

}