<?php

abstract class TestDd extends ProjectTestCase {

  /**
   * @var DdStructuresManager
   */
  static $sm;

  static $strName = 'a';

  static function setUpBeforeClass() {
    $_FILES = [
      'image' => [
        'tmp_name'  => TEMP_PATH.'/image.jpg'
      ]
    ];
    self::$sm = new DdStructuresManager;
    self::$sm->deleteByName(static::$strName);
    self::$sm->create([
      'title' => static::$strName,
      'name' => static::$strName
    ]);
    Sflm::reset();
    Sflm::setFrontendName('default');
  }

  static function tearDownAfterClass() {
    //self::$sm->deleteByName(static::$strName);
  }

}