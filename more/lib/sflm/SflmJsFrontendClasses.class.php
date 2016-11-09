<?php

/**
 * Сохранение и получение классов sflm-фронтенда
 */
class SflmJsFrontendClasses extends ArrayAccesseble {

  protected $frontend;

  function __construct(SflmFrontendJs $frontend) {
    $this->frontend = $frontend;
    $this->init();
  }

  function exists($class) {
    return in_array($class, $this->r);
  }

  function add($class, $source) {
    if (in_array($class, $this->r)) throw new Exception("$class exists");
    Sflm::log("Add frontend class '$class'. src: $source");
    $this->r[] = $class;
    return true;
  }

  function processCode($code, $source) {
    foreach (SflmJsClasses::parseValidClassesDefinition($code) as $class) {
      if (in_array($class, $this->r)) continue;
      Sflm::log("Add frontend class '$class'. src: $source");
      $this->r[] = $class;
    }
  }

  protected function init() {
    if (($r = $this->retrieve())) {
      $this->r = $r;
      return;
    }
    $this->load();
  }

  protected function load() {
    $this->r = [];
    foreach ($this->frontend->getPaths() as $path) {
      $classes = SflmJsClasses::parseValidClassesDefinition(Sflm::getCode($this->frontend->base->getAbsPath($path)));
      $this->r = array_merge($this->r, $classes);
    }
  }

  protected function retrieve() {
    return SflmCache::c()->load('sflmJsFrontendClasses'.$this->frontend->key());
  }

  function store() {
    if (!$this->r) Sflm::log('Storing existing objects. Nothing to store. Skipped');
    SflmCache::c()->save($this->r, 'sflmJsFrontendClasses'.$this->frontend->key());
  }

  function clean() {
    FileCache::c()->remove('sflmJsFrontendClasses'.$this->frontend->key());
    $this->r = [];
  }

}