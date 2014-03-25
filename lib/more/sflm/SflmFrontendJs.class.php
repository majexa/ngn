<?php

class SflmFrontendJs extends SflmFrontend {

  /**
   * @var SflmJsClasses
   */
  public $classes;

  protected function init() {
    $this->classes = new SflmJsClasses($this);
  }

  function processCode($code) {
    throw new Exception('Not using');
    $this->classes->processNgnClasses($code, explode("\n", getBacktrace(false))[1].' processing');
    return $code;
  }

  function processPath($path) {
    $this->classes->processPath($path);
  }

  function addObject($name, $source = 'direct', $strict = false) {
    $frontend = $this;
    $this->classes->addObject($name, $source, function($path) use ($frontend) {
      $frontend->addLib($path);
    }, function($source) use ($name, $frontend, $strict) {
      $s = "Class '$name' from '$source' not found";
      if ($strict) throw new Exception($s);
      $frontend->extraCode = "\n/*----------|$s|----------*/\n";
    });
  }

  protected function addPath($path, $addingFrom = '[not defined]') {
    if (Misc::hasPrefix('@', $path)) {
      $path = Misc::removePrefix('@', $path);
      $this->classes->processPath($path);
      return;
    }
    parent::addPath($path, $addingFrom);
  }

}