<?php

class ProjectTestRunner extends TestRunner {//

  protected $project;

  function __construct($project = null) {
    $this->project = $project ?: PROJECT_KEY;
    parent::__construct();
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function($v) {
      return ClassCore::hasAncestor($v, 'ProjectTestCase');
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