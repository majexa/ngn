<?php

Lib::addPearAutoloader('PHPUnit');
require_once 'PHPUnit/Autoload.php';
Lib::addPearAutoloader('PHP');
Lib::addPearAutoloader('File');
Lib::addPearAutoloader('Text');

class SimpleTestListener implements PHPUnit_Framework_TestListener {

  function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
    printf("Error while running test '%s'.\n", $test->getName());
  }

  function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
    printf("Test '%s' failed.\n", $test->getName());
  }

  function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    printf("Test '%s' is incomplete.\n", $test->getName());
  }

  function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    printf("Test '%s' has been skipped.\n", $test->getName());
  }

  function startTest(PHPUnit_Framework_Test $test) {
    printf("Test '%s' started.\n", $test->getName());
  }

  function endTest(PHPUnit_Framework_Test $test, $time) {
    printf("Test '%s' ended.\n", $test->getName());
  }

  function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
    printf("TestSuite '%s' started.\n", $suite->getName());
  }

  function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
    printf("TestSuite '%s' ended.\n", $suite->getName());
  }

}

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
      if (is_string($filterNames) and $filterNames[strlen($filterNames) - 1] == '*') {
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
      $filter = function ($class) {
        return Misc::hasPrefix('Test'.$this->filterPrefix, $class);
      };
    }
    elseif ($this->filterClasses) {
      $filter = function ($class) {
        return in_array($class, $this->filterClasses);
      };
    }
    $classes = array_map(function ($v) {
      return $v['class'];
    }, ClassCore::getDescendants('NgnTestCase', 'Test'));
    $classes = array_filter($classes, function($class) {
      return $class::enable();
    });
    if ($filter) $classes = array_filter($classes, $filter);
    return $classes;
  }

  protected function _run(array $classes) {
    output("running tests: ".implode(', ', $classes));
    //return;
    foreach ($classes as $class) {
      $this->addTestSuite($class);
    }
    $this->__run();
  }

  protected function __run() {
    PHPUnit_TextUI_TestRunner::run($this->suite, [
      'stopOnError' => true,
      'listeners'   => [

      ]
    ]);
  }

  static $folder;

}

TestRunnerAbstract::$folder = __DIR__;