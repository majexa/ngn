<?php

class TestRunnerNgn extends TestRunnerAbstract {

  protected function getClasses() {
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

}