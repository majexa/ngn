<?php

class TestFieldFile extends TestFieldDd {

  static function enable() {
    return false;
  }

  function createData() {
    return ['sample' => TestRunnerNgn::tempImageFixture()];
  }

  function runTests($request = false) {
    output("request: $request");
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'] == '/u/dd/a/1/sample.jpg');
    $this->updateItem(['sample' => TestRunnerNgn::tempImageFixture()], $request);
    print static::$im->form->html();
  }

}