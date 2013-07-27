<?php

class SflmJsClasses {

  public $classesPaths, $existingClasses, $frontend;

  function __construct(SflmFrontendJs $frontend) {
    $this->frontend = $frontend;
    $this->initClassesPaths();
    $this->initExistingClasses();
  }

  protected function initClassesPaths() {
    if (($this->classesPaths = NgnCache::c()->load('jsClassesPaths'))) return;
    $this->classesPaths = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) $files = Arr::append($files, Dir::getFilesR($path, '[A-Z]*.js'));
    foreach ($files as $file) $this->classesPaths[Misc::removeSuffix('.js', basename($file))] = $this->frontend->sflm->getPath($file);
    NgnCache::c()->save($this->classesPaths, 'jsClassesPaths');
  }

  protected function initExistingClasses() {
    if (($this->existingClasses = NgnCache::c()->load('jsExistingClasses'))) return;
    $this->existingClasses = [];
    $storedPaths = [];
    foreach ($this->frontend->getPaths() as $path) if (preg_match('/.*\/[A-Z][A-Za-z.]+\.js/', $path)) $storedPaths[] = $path;
    foreach ($storedPaths as $path) $this->addClassesToExisting($path);
  }

  protected function addClassesToExisting($path) {
    $this->existingClasses = array_merge($this->existingClasses, $this->parseClasses(file_get_contents($this->frontend->sflm->getAbsPath($path))));
  }

  protected function parseClasses($c) {
    if (preg_match_all('/([A-Za-z.]+)\s+=\s+new Class/', $c, $m)) return $m[1];
    return [];
  }

  protected function parseParentClasses($c) {
    if (preg_match_all('/Extends:\s+([A-Z][A-Za-z.]+)/', $c, $m)) return $m[1];
    return [];
  }

  protected function parseRequiredClasses($c, $k = '') {
    $r = [];
    if (preg_match_all('/@requires'.ucfirst($k).'\s+([A-Za-z., ]+)/', $c, $m)) {
      foreach ($m[1] as $v) $r = array_merge($r, array_map('trim', explode(',', $v)));
    }
    return $r;
  }

  protected function parseRequiredAfterClasses($c) {
    return $this->parseRequiredClasses($c, 'after');
    // does not work properly
    //if (preg_match_all('/new\s+([A-Z][A-Za-z.]+)/', $c, $m)) {
      //foreach ($m[1] as $class) if ($class != 'Class') $r[] = $class;
    //}
  }

  function addClass($class) {
    //prr("add class $class");
    if (in_array($class, $this->existingClasses)) return;
    if (!isset($this->classesPaths[$class])) throw new Exception("File for class '$class' does not exists");
    $c = file_get_contents($this->frontend->sflm->getAbsPath($this->classesPaths[$class]));
    foreach ($this->parseClasses($c) as $v) $this->existingClasses[] = $v;
    foreach ($this->parseParentClasses($c) as $v) $this->addClass($v);
    foreach ($this->parseRequiredClasses($c) as $v) $this->addClass($v);
    $this->frontend->addLib($this->classesPaths[$class]);
    foreach ($this->parseRequiredAfterClasses($c) as $v) $this->addClass($v);
    NgnCache::c()->save($this->existingClasses, 'jsExistingClasses');
    $this->frontend->incrementVersion();
  }

}