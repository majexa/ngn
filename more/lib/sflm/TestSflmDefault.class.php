<?php

class TestSflmDefault extends ProjectTestCase {

  static function setUpBeforeClass() {
    Sflm::setFrontend('default');
  }

  protected function setUp() {
    Sflm::clearCache();
  }

  function test() {
    Sflm::frontend('js')->getTags();
  }

}