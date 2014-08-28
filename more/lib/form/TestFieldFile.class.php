<?php

class TestFieldFile extends TestFieldAbstract {

  static function enable() {
    return false;
  }

  function createData() {
    return ['sample' => TestRunnerNgn::tempImageFixture()];
  }

  function runTests($request = false) {
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'] == '/u/dd/a/1/sample.jpg');
    print static::$im->form->html();
    //$this->assertTrue((bool)strstr(static::$im->form->html(), 'name="sample" value="'.$this->v1.'"'));
    //prr($item);
  }

}