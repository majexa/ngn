<?php

class ProjectTestRunner extends TestRunner {

  protected $project;

  function __construct($project) {
    $this->project = $project;
    parent::__construct();
  }

  protected function getClasses() {
    $classes = [];
    foreach (parent::getClasses() as $class) {
      if (strstr(Lib::getClassPath($class), "projects/$this->project/")) $classes[] = $class;
    }
    return $classes;
  }

}