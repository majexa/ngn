<?php

class ProjectTestPrinter extends PHPUnit_TextUI_ResultPrinter {

  protected $projectName;

  function __construct($projectName) {
    Misc::checkEmpty($projectName);
    $this->projectName = $projectName;
  }

  protected function printDefectHeader(PHPUnit_Framework_TestFailure $defect, $count) {
    $failedTest = $defect->failedTest();
    $testName = $failedTest instanceof PHPUnit_Framework_SelfDescribing ? $failedTest->toString() : get_class($failedTest);
    $this->write(sprintf("%d) project \"%s\": %s\n", $count, $this->projectName, $testName));
  }

  protected function _printDefectTrace(PHPUnit_Framework_TestFailure $defect) {
    $this->write($defect->thrownException()->getMessage());
  }

}