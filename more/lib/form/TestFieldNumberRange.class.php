<?php

class TestFieldNumberRange extends TestFieldDd {

  function createData() {
    return [
      'sample' => [
        'from' => 10,
        'to'   => 20,
      ]
    ];
  }

  function runTests($request = false) {
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample']['from'] == 10);
    $this->assertTrue($item['sample']['to'] == 20);
    $this->updateItem([
      'sample' => [
        'from' => 20,
        'to'   => 30,
      ]
    ], $request);
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample']['from'] == 20);
    $this->assertTrue($item['sample']['to'] == 30);
  }

}