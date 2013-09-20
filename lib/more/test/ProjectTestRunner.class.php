<?php

class ProjectTestRunner extends TestRunner {

  protected $project;

  function __construct($project) {
    $this->project = $project;
    parent::__construct();
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function($class) {
      return strstr(Lib::getClassPath($class), "projects/$this->project/");
    });
  }

}