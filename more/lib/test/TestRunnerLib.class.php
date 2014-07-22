<?php

class TestRunnerLib extends TestRunnerNgn {

  protected $libPath;

  static function replace($path) {
    foreach (['NGN_PATH', 'NGN_ENV_PATH'] as $v) $path = str_replace($v, constant($v), $path);
    return $path;
  }

  function __construct($libPath, $filterNames = null) {
    $r = static::replace($libPath);
    if (Misc::hasSuffix('.php', $r)) $r = dirname($r);
    $this->libPath = $r;
    parent::__construct($filterNames);
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