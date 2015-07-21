<?php

class TestFieldDateSchedule extends TestFieldDd {
  static function setUpBeforeClass() {

    // (new DdForm(new DdFields('a'), 'a'))->debugElements();
    die2((new DdFields('a'))->getFields());
  }

  function createData() {


    return [
      'sample' => <<<TEXT
10.06.2005 10:00 11:00 12:00
10.06.2005 10:00-18:00
10.06.2005-12.062005 10:00-18:00
TEXT
    ];
  }

  function runTests($request = false) {
    $item = static::$im->items->getItem($this->itemId);
    print_r($item);
  }

}