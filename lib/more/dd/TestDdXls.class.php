<?php

class TestDdXls extends TestDd {

  function test() {
    $im = DdItemsManager::getDefault('a');
    for ($i=0; $i<50; $i++) $im->create(['a' => time()]);
    LongJobCore::run(new DdXls($im->items));
    sleep(20);
    prrLongJobCore::state($this->getLongJob()->id())->all();
  }

  //static function setUpBeforeClass() {
  //}
  static function tearDownAfterClass() {
  }

}