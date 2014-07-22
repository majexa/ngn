<?php

class TestFieldUser extends TestFieldAbstract {

  function createData() {
    return ['sample' => 1];
  }

  function runTests($request = false) {
    $this->assertTrue(static::$im->items->getItemF($this->itemId)['sample']['id'] == 1);
  }

}