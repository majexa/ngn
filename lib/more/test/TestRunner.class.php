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
    //$path = Lib::getPath($class);
    //if (file_exists(dirname($path).'/init.php')) require_once dirname($path).'/init.php';
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) return;
    $this->suite->addTestSuite($rc);
  }

  function __call($name, $args) {
    if (method_exists($this, "_$name")) call_user_func_array([$this, "_$name"], $args);
    else $this->_test($name);
  }

  protected function getClasses() {
    return array_map(function ($v) {
      return $v['class'];
    }, ClassCore::getDescendants('NgnTestCase', 'Test'));
  }

  function _global(array $names = null) {
    foreach ($this->getClasses() as $class) $this->addTestSuite($class);
    $this->run();
  }

  function _local($lib) {
    //foreach (explode(',', $names) as $name) $this->addTestSuite('Test'.ucfirst($name));
    $this->run();
  }

  protected function _run(array $classes) {
    foreach ($classes as $class) $this->addTestSuite($class);
    $this->run();
  }

  /*
  function _list($name) {
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
  */

  protected function run() {
    PHPUnit_TextUI_TestRunner::run($this->suite);
  }

  static $folder;

}

TestRunner::$folder = __DIR__;