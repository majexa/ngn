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

  function addClass($class, $source = 'direct', $strict = false) {
    if (strstr($class, 'User')) die2(31);
    $frontend = $this;
    $this->classes->addClass($class, $source, function($path) use ($frontend) {
      $frontend->addLib($path);
    }, function($source) use ($class, $frontend, $strict) {
      $s = "Class '$class' from '$source' not found";
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