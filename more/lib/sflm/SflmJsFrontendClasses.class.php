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
    //if (in_array($class, $this->r)) {
    //  Sflm::output("Skipped adding frontend class '$class' (src: $source). EXISTS");
    //  return false;
    //}
    Sflm::output("Add frontend class '$class' (src: $source)");
    $this->r[] = $class;
    $this->afterAdd($class);
    return true;
  }

  function processCode($code, $source) {
    $n = 0;
    foreach (SflmJsClasses::parseValidClassesDefinition($code) as $class) {
      if (in_array($class, $this->r)) continue;
      Sflm::output("Add frontend class '$class' (src: $source)");
      $this->r[] = $class;
      $n++;
    }
    if ($n) $this->afterAdd("code from $source");
  }

  protected function afterAdd($caption) {
    $this->store();
    if ($this->frontend->incrementVersion()) Sflm::output("Increment version on storing '$caption'");
  }

  protected function init() {
    if (($r = $this->retrieve())) {
      $this->r = $r;
      return;
    }
    $this->r = [];
    foreach ($this->frontend->getPaths() as $path) {
      $classes = SflmJsClasses::parseValidClassesDefinition(Sflm::getCode($this->frontend->base->getAbsPath($path)));
      $this->r = array_merge($this->r, $classes);
    }
  }

  protected function retrieve() {
    return SflmCache::c()->load('jsFrontendClasses'.$this->frontend->key());
  }

  protected function store() {
    if (!$this->r) Sflm::output('Storing existing objects. Nothing to store. Skipped');
    SflmCache::c()->save($this->r, 'jsFrontendClasses'.$this->frontend->key());
  }

}