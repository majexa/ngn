<?php

class TestFieldDdTagsBase extends TestDd {

  /**
   * @var DdItemsManager
   */
  static $im;

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    $fm = O::gett('DdFieldsManager', 'a');
    $fieldType = lcfirst(Misc::removePrefix('TestField', get_called_class()));
    $fm->create([
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => $fieldType
    ]);
    self::$im = DdItemsManager::getDefault('a');
  }

  protected $v1 = 'one', $v2 = 'two';

}