<?php

class SflmFrontendJs extends SflmFrontend {

  /**
   * @var SflmJsClasses
   */
  public $classes;

  protected function init() {
    $this->classes = new SflmJsClasses($this);
  }

  function addClass($class, $source = 'default') {
    $frontend = $this;
    $this->classes->addClass($class, $source, function($path) use ($frontend) {
      $frontend->addLib($path);
    }, function($source) use ($class, $frontend) {
      die2("\n/*----------|Class '$class' from '$source' not found|----------*/\n");
      $frontend->extraCode = "\n/*----------|Class '$class' from '$source' not found|----------*/\n";
    });
  }

  protected function addPath($path) {
    if (Misc::hasPrefix('@', $path)) {
      $path = Misc::removePrefix('@', $path);
      $this->classes->processPath($path);
      return;
    }
    parent::addPath($path);
  }

  protected function code_____() {
    return parent::code()."\nNgn.sflVersion = { js: '.$this->version().', css: '.Sflm::get('css', $this->frontend)->version().' };\n";
  }

}