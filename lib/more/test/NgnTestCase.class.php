<?php

abstract class NgnTestCase extends PHPUnit_Framework_TestCase {

  function __construct($name = NULL, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);
  }

  static function setUpBeforeClass() {}
  
  static function tearDownAfterClass() {}

}
