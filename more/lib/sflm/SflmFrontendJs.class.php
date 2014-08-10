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

  function addPath($path, $source = 'direct') {
    $this->classes->processPath($path, $source);
  }

  function _addLib($lib) {
    if (!$this->base->exists($lib)) throw new Exception("Lib '$lib' does not exists");
    Sflm::output("Adding lib '$lib'");
    foreach ($this->base->getPaths($lib) as $path) $this->_addPath($path);
    return $this;
  }

  function addClass($name, $source = 'direct', $strict = false) {
    if ($this->stored) throw new Exception("Can't add after frontend was stored. Reset or rerun frontend");
    $this->classes->addClass($name, $source, $strict);
  }

  /**
   * @param string $str Object/Class name or is pars; path or part of it
   * @return bool
   */
  function exists($str) {
    return Arr::strExists(Sflm::frontend('js')->getPaths(), $str);
  }

  protected function getStaticPaths() {
    return $this->base->getPaths('core', true);
  }

}