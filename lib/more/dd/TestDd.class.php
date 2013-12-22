<?php

abstract class TestDd extends ProjectTestCase {

  /**
   * @var DdStructuresManager
   */
  static $sm;

  static function setUpBeforeClass() {
    $_FILES = [
      'image' => [
        'tmp_name'  => TEMP_PATH.'/image.jpg'
      ]
    ];
    self::$sm = new DdStructuresManager;
    self::$sm->deleteByName('a');
    self::$sm->create([
      'title' => 'a',
      'name' => 'a'
    ]);
    Sflm::setFrontend('default');
  }

  static function tearDownAfterClass() {
    self::$sm->deleteByName('a');
  }

}