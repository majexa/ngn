<?php

class TestRunnerProject extends TestRunnerAbstract {

  protected $projectName;

  function __construct($projectName, $filterNames = null) {
    $this->projectName = $projectName;
    parent::__construct($filterNames);
  }

  protected function run() {
    PHPUnit_TextUI_TestRunner::run($this->suite, [
        'printer' => new ProjectTestPrinter($this->projectName)
      ]);
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function ($class) {
      $r = ClassCore::hasAncestor($class, 'ProjectTestCase');
      return $r;
    });
  }

  /**
   * Запускает проектные тесты, находящиеся в папке проекта
   *
   * @param bool $checkErrors
   */
  function l() {
    //if ($checkErrors) (new Errors)->clear();
    $classes = array_filter($this->getClasses(), function ($class) {
      return strstr(Lib::getClassPath($class), "projects/$this->projectName/") or !empty($class::$local);
    });
    //if ($checkErrors) $classes[] = 'TestProjectAllErrors';
    $this->_run($classes);
  }

  /**
   * Запускает все проектные тесты, находящиеся не в папке проекта
   */
  function g() {
    $this->_run(array_filter($this->getClasses(), function ($class) {
      return !strstr(Lib::getClassPath($class), "projects/$this->projectName/");
    }));
  }

  /**
   * Запускает все проектные тесты
   */
  function a() {
    $this->_run($this->getClasses());
  }

}