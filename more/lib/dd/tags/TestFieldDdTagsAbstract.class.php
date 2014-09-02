<?php

abstract class TestFieldDdTagsAbstract extends TestFieldDd {

  protected $v1 = 'one', $v2 = 'two', $v3 = 'three', $itemId;

  function createTags() {
  }

  function test() {
    $this->createTags();
    $this->createItem();
    $this->runTests();
    $this->updateItem(['sample' => '']);
    self::$im = DdCore::imDefault('a');
    $this->createItem(true);
    $this->runTests();
  }

}