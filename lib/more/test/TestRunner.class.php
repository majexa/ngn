<?php

require_once __DIR__.'/TestRunnerAbstract.class.php';

class TestRunner extends TestRunnerAbstract {

  protected function getClasses() {
    return array_filter(parent::getClasses(), function ($v) {
      return !is_subclass_of($v, 'ProjectTestCase');
    });
  }

  /**
   * Запускает все тесты ядра
   */
  function globl() {
    $this->_run($this->getClasses());
  }

  /**
   * Запускает все тесты указанной библиотеки
   */
  function locl($libPath) {
    $this->_run(array_filter($this->getClasses(), function($v) use ($libPath) {
      return strstr(Lib::getClassPath($v), $libPath);
    }));
  }

}