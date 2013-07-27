<?php

class SflmFrontendJs extends SflmFrontend {

  public $classes;

  protected function init() {
    $this->classes = new SflmJsClasses($this);
  }

  function addClass($class) {
    $this->classes->addClass($class);
  }

}