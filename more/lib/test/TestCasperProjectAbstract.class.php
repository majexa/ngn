<?php

abstract class TestCasperProjectAbstract extends ProjectTestCase {

  static function setUpBeforeClass() {
    $p = PROJECT_KEY;
    print `pm localProject cc $p`;
  }

  protected function casper(array $steps) {
    Casper::run(PROJECT_KEY, $steps);
  }

}
