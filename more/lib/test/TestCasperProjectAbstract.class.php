<?php

class TestCasperProjectAbstract extends ProjectTestCase {

  static function setUpBeforeClass() {
    $p = PROJECT_KEY;
    `pm localProject cc $p`;
  }

  protected function casper($cmds) {
    Casper::run(PROJECT_KEY, $cmds);
  }

}
