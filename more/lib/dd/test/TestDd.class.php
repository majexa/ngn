<?php

abstract class TestDd extends ProjectTestCase {

  /**
   * @var DdStructuresManager
   */
  static $sm;

  static $strName = 'a';

  static function setUpBeforeClass() {
    Sflm::$disable = true;
    Sflm::$output = false;
    $_FILES = [
      'image' => TestCore::tempImageFixture()
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