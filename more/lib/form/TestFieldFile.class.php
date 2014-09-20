<?php

class TestFieldFile extends TestFieldDd {

  static function enable() {
    return false;
  }

  function createData() {
    return ['sample' => TestCore::tempImageFixture()];
  }

  function runTests($request = false) {
    output("request: $request");
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'] == '/u/dd/a/1/sample.jpg');
    $this->updateItem(['sample' => TestCore::tempImageFixture()], $request);
    print static::$im->form->html();
  }

}