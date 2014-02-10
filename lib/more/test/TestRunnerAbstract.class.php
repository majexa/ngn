<?php

Lib::addPearAutoloader('PHPUnit');
require_once 'PHPUnit/Autoload.php';
Lib::addPearAutoloader('PHP');

class TestRunnerAbstract {

  protected $filterClasses = [], $filterPrefix;

  /**
   * @var PHPUnit_Framework_TestSuite
   */
  protected $suite;

  function __construct($filterNames = null) {
    R::set('plainText', true);
    $this->suite = new PHPUnit_Framework_TestSuite('one');
    if ($filterNames) {
      if (is_string($filterNames) and $filterNames[strlen($filterNames)-1] == '*') {
        $this->filterPrefix = ucfirst(rtrim($filterNames, '*'));
        return;
      }
      $filterNames = (array)$filterNames;
      foreach ($filterNames as $v) $this->filterClasses[] = 'Test'.ucfirst($v);
    }
  }

  protected function addTestSuite($class) {
    $rc = new ReflectionClass($class);
    if ($rc->isAbstract()) return;
    $this->suite->addTestSuite($rc);
  }

  protected function getClasses() {
    $filter = false;
    if (isset($this->filterPrefix)) {
      $filter = function($class) {
        return Misc::hasPrefix('Test'.$this->filterPrefix, $class);
      };
    } elseif ($this->filterClasses) {
      $filter = function($class) {
        return in_array($class, $this->filterClasses);
      };
    }
    $r = array_map(function ($v) {
      return $v['class'];
    }, ClassCore::getDescendants('NgnTestCase', 'Test'));
    if ($filter) $r = array_filter($r, $filter);
    die2($r);
    return $r;
  }

  protected function _run(array $classes) {
    output("running tests: ".implode(', ', $classes));
    foreach ($classes as $class) $this->addTestSuite($class);
    $this->run();
  }

  protected function run() {
    PHPUnit_TextUI_TestRunner::run($this->suite);
  }

  static $folder;

}

TestRunnerAbstract::$folder = __DIR__;