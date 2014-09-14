<?php

abstract class TestFieldDdTagsAbstract extends TestFieldDd {

  protected $v1 = 'one', $v2 = 'two', $v3 = 'three', $itemId;
  static $tagFieldName = 'sample';

  function createTags() {
  }

  function test() {
    $this->createTags();
    $this->createItem();
    $this->runTests();
    $this->updateItem([static::$tagFieldName => '']);
    self::$im = DdCore::imDefault(static::$strName);
    $this->createItem(true);
    $this->runTests();
  }

}