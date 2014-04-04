<?php

class SflmFrontendJs extends SflmFrontend {

  /**
   * @var SflmJsClasses
   */
  public $classes;

  protected function init() {
    $this->classes = new SflmJsClasses($this);
  }

  protected function __addPath($path, $source = null) {
    $this->addPath($path, $source);
  }

  function addPath($path, $source) {
    $this->classes->processPath($path, $source);
  }

  function _addLib($lib) {
    if (!$this->sflm->exists($lib)) throw new Exception("Lib '$lib' does not exists");
    Sflm::output("Adding lib '$lib'");
    foreach ($this->sflm->getPaths($lib) as $path) $this->_addPath($path);
    return $this;
  }

  function addObject($name, $source = 'direct', $strict = false) {
    if ($this->stored) throw new Exception("Can't add after frontend was stored. Reset or rerun frontend");
    $this->classes->addObject($name, $source, function($source) use ($name, $strict) {
      $s = "Class '$name' from '$source' not found";
      if ($strict) throw new Exception($s);
      $this->extraCode = "\n/*----------|$s|----------*/\n";
    }, function($path) {
      $this->_addPath($path);
    });
  }

  /**
   * @param string $str Object/Class name or is pars; path or part of it
   * @return bool
   */
  function exists($str) {
    return Arr::strExists(Sflm::frontend('js')->getPaths(), $str);
  }

}