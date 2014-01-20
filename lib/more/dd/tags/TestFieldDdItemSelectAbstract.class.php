<?php

abstract class TestFieldDdItemSelectAbstract extends TestFieldDdTagsAbstract {

  /**
   * @var DdStructuresManager
   */
  static $sm2;

  /**
   * @var DdItemsManager
   */
  static $im2;

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$sm2 = new DdStructuresManager;
    self::$sm2->deleteByName('b');
    self::$sm2->create([
      'title' => 'b',
      'name' => 'b'
    ]);
    $fm = O::gett('DdFieldsManager', 'b');
    $fm->create([
      'name'  => 'title',
      'title' => 'title',
      'type'  => 'text'
    ]);
    self::$im2 = DdCore::imDefault('b');
  }

  static protected function fieldData($fieldType) {
    return array_merge(parent::fieldData($fieldType), [
      'settings' => [
        'strName' => 'b'
      ]
    ]);
  }

  static function tearDownAfterClass() {
    parent::tearDownAfterClass();
    self::$sm->deleteByName('b');
  }

}