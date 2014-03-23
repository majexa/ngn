<?php

class MockLib extends Lib {

  static $letter = 'A', $list;

  static function initClassesList() {
    return [
      [
        static::$letter => [
          'path' => ''.static::$letter.'/.class.php',
          'file' => static::$letter.'.class.php'
        ]
      ]
    ];
  }

}

class TestLib {

  function test() {
    MockLib::$list = false;
    die2(count(MockLib::getClassesListCached()));
    MockLib::$letter = 'B';
    prrc(MockLib::getClassesListCached());
  }

}