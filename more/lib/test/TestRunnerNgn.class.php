<?php

/**
 * Запуск тестов фреймворка
 */
class TestRunnerNgn extends TestRunnerAbstract {

  function _getClasses() {
    return array_filter(parent::getClasses(), function ($v) {
      return !is_subclass_of($v, 'ProjectTestCase');
    });
  }

  /**
   * Запускает все тесты ядра
   */
  function run() {
    $this->_run($this->getClasses());
  }

  static function tempImageFixture() {
    $file = TEMP_PATH.'/'.time();
    copy(MORE_PATH.'/lib/test/fixture/image.jpg', $file);
    return [
      'name' => 'test',
      'tmp_name' => $file
    ];
  }

}