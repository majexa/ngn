<?php

abstract class NgnTestCase extends PHPUnit_Framework_TestCase {

  function __construct($name = null, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);
    Sflm::setFrontendName('default', true);
  }

  protected function cmd($cmd) {
    $r = `$cmd`;
    if (($error = CliTestRunner::detectError($r))) throw new Exception("$error in text:\n$r");
  }

  static function enable() {
    return true;
  }

  static function setUpBeforeClass() {}
  
  static function tearDownAfterClass() {}

}
