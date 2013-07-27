<?php

class NgnWebTestCase extends NgnTestCase {

  /**
   * @var NgnSelenium
   */
  static $s;
  
  static $site;
  
  static function setUpBeforeClass() {
    static::$s = new NgnSelenium("*firefox", "http://".static::$site."/");
    static::$s->start();
  }
  
  static function tearDownAfterClass() {
    static::$s->stop();
  }

}