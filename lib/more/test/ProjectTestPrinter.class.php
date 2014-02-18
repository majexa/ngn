<?php

class ProjectTestPrinter extends PHPUnit_TextUI_ResultPrinter {

  protected $projectName;

  function __construct($projectName) {
    Misc::checkEmpty($projectName);
    $this->projectName = $projectName;
  }

  protected function printDefectHeader(PHPUnit_Framework_TestFailure $defect, $count) {
    $failedTest = $defect->failedTest();
    if ($failedTest instanceof PHPUnit_Framework_SelfDescribing) {
      $testName = $failedTest->toString();
    }
    else {
      $testName = get_class($failedTest);
    }
    $this->write(sprintf("%d) Project \"%s\": %s\n", $count, $this->projectName, $testName));
  }

}