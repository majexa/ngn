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
    $this->log("Adding lib '$lib'");
    foreach ($this->base->getPaths($lib) as $path) $this->_addPath($path);
    return $this;
  }

  function addClass($name, $source = 'direct', $strict = false) {
    $this->checkNotStored();
    return $this->classes->addClass($name, $source, $strict);
  }

  /**
   * @param string $str Class name or its part; path or part of it
   * @return bool
   */
  function exists($str) {
    return Arr::strExists(Sflm::frontend('js')->getPaths(), $str);
  }

  protected function getStaticPaths() {
    return $this->base->getPaths('core', true);
  }

  function store($source = 'direct') {
    parent::store($source);
    $this->classes->frontendClasses->store();
  }

  function processCode($code, $source) {
    $this->checkNotStored();
    $this->classes->processCode($code, $source);
  }

  function processHtml($html, $source) {
    $this->checkNotStored();
    if (!preg_match_all('!<script>(.*)</script>!s', $html, $m)) return false;
    foreach ($m[1] as $code) $this->processCode($code, $source);
    return $html;
  }

  function _code() {
    $code = parent::_code();
    $mtDependencies = O::get('SflmMtDependencies', NGN_ENV_PATH.'/mootools');
    $mtCode = $mtDependencies->contents('Core');
    $mtCode .= $mtDependencies->parse($code);
    return $mtCode."\n".$code;
  }

}