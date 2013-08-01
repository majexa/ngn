<?php

class SflmFrontendJs extends SflmFrontend {

  public $classes;

  protected function init() {
    $this->classes = new SflmJsClasses($this);
  }

  function addClass($class) {
    $this->classes->addClass($class);
  }

  protected function code_____() {
    return parent::code()."\nNgn.sflVersion = { js: '.$this->version().', css: '.Sflm::get('css', $this->frontend)->version().' };\n";
  }

}