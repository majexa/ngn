<?php

class ProjectTestRunner extends TestRunnerAbstract {

  protected $project, $filterClasses = [];

  function __construct(array $filterNames = null) {
    $this->project = PROJECT_KEY;
    if ($filterNames) foreach ($filterNames as $v) $this->filterClasses[] = 'Test'.ucfirst($v);
    parent::__construct();
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function($class) {
      $r = ClassCore::hasAncestor($class, 'ProjectTestCase');
      if ($r and $this->filterClasses) $r = in_array($class, $this->filterClasses);
      return $r;
    });
  }

  function _projectLocal() {
    $this->_run(array_filter($this->getClasses(), function($v) {
      return strstr(Lib::getClassPath($v), "projects/$this->project/");
    }));
  }

  function _projectGlobal() {
    $this->_run(array_filter($this->getClasses(), function($v) {
      return !strstr(Lib::getClassPath($v), "projects/$this->project/");
    }));
  }

  function _project() {
    $this->_run($this->getClasses());
  }

}