<?php

abstract class TestFieldDdTagsAbstract extends TestDd {

  /**
   * @var DdItemsManager
   */
  static $im;

  /**
   * @var DdFieldsManager
   */
  static $fm;

  static $fieldId;

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$fm = O::gett('DdFieldsManager', 'a');
    $fieldType = lcfirst(Misc::removePrefix('TestField', get_called_class()));
    self::$fieldId = self::$fm->create(static::fieldData($fieldType));
    self::$im = DdCore::imDefault('a');
  }

  static protected function fieldData($fieldType) {
    return [
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => $fieldType
    ];
  }

  protected $v1 = 'one', $v2 = 'two', $v3 = 'three', $itemId;

  function createTags() {
  }

  /**
   * @return integer Item ID
   */
  abstract function createItem();

  abstract function runTests();

  function test() {
    $this->createTags();
    $this->itemId = $this->createItem();
    $this->runTests();
  }

}