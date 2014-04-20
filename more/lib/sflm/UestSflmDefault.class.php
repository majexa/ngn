<?php

class UestSflmDefault extends ProjectTestCase {

  static function setUpBeforeClass() {
    Sflm::setFrontendName('default');
  }

  protected function setUp() {
    Sflm::clearCache();
  }

  function test() {
    Sflm::frontend('js')->getTags();
  }

}