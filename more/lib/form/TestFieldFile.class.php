<?php

class TestFieldFile extends TestFieldDd {

  static function enable() {
    return true;
  }

  function createData() {
    $r = [
      'sample' => TestCore::tempImageFixture(),
      'sample2' => TestCore::tempImageFixture()
    ];
    return $r;
  }

  function runTests($request = false) {
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'] == '/u/dd/a/1/sample.jpg');
    $this->assertTrue($item['sample2'] == '/u/dd/a/1/sample2.jpg');
    //$this->updateItem(['sample' => TestCore::tempImageFixture()], $request);
    //print static::$im->form->html();
  }

}