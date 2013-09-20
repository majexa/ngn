<?php

Lib::addPearAutoloader('PHPUnit');
require_once 'PHPUnit/Autoload.php';
Lib::addPearAutoloader('PHP');

class TestRunner {

  /**
   * @var PHPUnit_Framework_TestSuite
   */
  protected $suite;

  function __construct() {
    R::set('plainText', true);
    $this->suite = new PHPUnit_Framework_TestSuite('one');
  }

  function addTestSuite($class) {
    $path = Lib::getPath($class);
    if (file_exists(dirname($path).'/init.php')) require_once dirname($path).'/init.php';
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) return;
    $this->suite->addTestSuite($rc);
  }

  function __call($name, $args) {
    if (method_exists($this, "_$name")) call_user_func_array([$this, "_$name"], $args);
    else $this->_test($name);
  }

  /**
   * Определяет является ли тест тестом проекта или тестом ядра (core, more, site, sb, etc.)
   *
   * @param $test
   */
  protected function isCoreTest($class) {
    prr(Lib::getClassPath($class));
  }

  protected function getClasses() {
    return array_map(function ($v) {
      return $v['class'];
    }, ClassCore::getDescendants('NgnTestCase', 'Test'));
  }

  function _all() {
    foreach ($this->getClasses() as $class) $this->addTestSuite($class);
    $this->run();
  }

  function _test($names) {
    foreach (explode(',', $names) as $name) $this->addTestSuite('Test'.ucfirst($name));
    $this->run();
  }

  function _lst($name) {
    $names = include dirname(__DIR__)."/list/$name.php";
    foreach ($names as $name) {
      $this->addTestSuite('Test'.ucfirst($name));
    }
    $this->run();
  }

  function _folder($name) {
    $files = Dir::getOrderedFiles(__DIR__."/$name", 'Test*');
    foreach ($files as $file) $this->addTestSuite(basename($file, '.class.php'));
    PHPUnit_TextUI_TestRunner::run($this->suite, [
      'stopOnIncomplete' => true,
      'stopOnError'      => true
    ]);
  }

  protected function run() {
    $result = PHPUnit_TextUI_TestRunner::run($this->suite);
    if ($result->failureCount()) {
      foreach ($result->failures() as $failure) {
        /* @var $failure PHPUnit_Framework_TestFailure */
        $failure->getExceptionAsString();
      }
    }
  }

  static $folder;

}

TestRunner::$folder = __DIR__;