<?php

/**
 * Запуск тестов библиотек
 */
class TestRunnerLib extends TestRunnerNgn {

  protected $libPath;

  static function replace($path) {
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function __construct($libPath, $filterNames = null) {
    $libPath = static::replace($libPath);
    if (Misc::hasSuffix('.php', $libPath)) $libPath = dirname($libPath);
    $this->libPath = $libPath;
    parent::__construct($filterNames);
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function($v) {
      return strstr(Lib::getClassPath($v), $this->libPath);
    });
  }

  /**
   * Запускает все тесты указанной библиотеки
   */
  function run() {
    $this->_run($this->getClasses());
  }

  protected function run2() {
    $this->_run($this->getClasses());
  }

}