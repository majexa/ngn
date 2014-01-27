<?php

class TestFieldDdCity extends TestFieldDdTagsAbstract {

  const nnovTagId = 822, russiaTagId = 300;

  function createItem() {
    return static::$im->create(['sample' => self::nnovTagId]);

  }

  function runTests() {
    //die2(static::$im->items->getItem($this->itemId));
  }

  //function test() {
    //$items = new DdItems('a');
    //$items->addTagFilter('sample', self::nnovTagId);
    //die2($items->getItems());
  //}

}