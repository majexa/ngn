<?php

class TestRunnerLib extends TestRunnerNgn {

  protected $libPath;

  function __construct($libPath, $filterNames = null) {
    $this->libPath = $libPath;
    parent::__construct($filterNames);
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function ($v) {
      return !is_subclass_of($v, 'ProjectTestCase');
    });
  }

  /**
   * Запускает все тесты указанной библиотеки
   */
  function run() {
    $this->_run(array_filter($this->getClasses(), function($v) {
      return strstr(Lib::getClassPath($v), $this->libPath);
    }));
  }

}