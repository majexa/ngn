<?php

class SflmJsClassesBase {

  public $existingClasses, $existingClassesPaths, $classesPaths, $frontend;

  function __construct(SflmFrontendJs $frontend) {
    $this->frontend = $frontend;
    $this->initExistingClasses();
    $this->initClassesPaths();
  }

  protected function initExistingClasses() {
    if (($r = NgnCache::c()->load('jsExistingClasses'.$this->frontend->frontend))) {
      list($this->existingClassesPaths, $this->existingClasses) = $r;
      return;
    }
    $this->existingClassesPaths = [];
    $this->existingClasses = [];
    $storedPaths = [];
    foreach ($this->frontend->getPaths() as $path) if (preg_match('/.*\/[A-Za-z.]+\.js/', $path)) {
      $storedPaths[] = $path;
    }
    //foreach ($this->frontend->getPaths() as $path) if (preg_match('/.*\/[A-Z][A-Za-z.]+\.js/', $path)) $storedPaths[] = $path;
    foreach ($storedPaths as $path) {
      $classes = $this->parseClassesDefinition(file_get_contents($this->frontend->sflm->getAbsPath($path)));
      foreach ($classes as $class) $this->existingClassesPaths[$class] = $path;
      $this->existingClasses = array_merge($this->existingClasses, $classes);
    }
    NgnCache::c()->save([$this->existingClassesPaths, $this->existingClasses], 'jsExistingClasses'.$this->frontend->frontend);
  }

  protected function initClassesPaths() {
    if (($this->classesPaths = NgnCache::c()->load('jsClassesPaths'))) return;
    $this->_initClassesPaths();
  }

  protected function _initClassesPaths() {
    $classesPaths = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) $files = Arr::append($files, Dir::getFilesR($path, '[A-Z]*.js'));
    foreach ($files as $file) $classesPaths[Misc::removeSuffix('.js', basename($file))] = $this->frontend->sflm->getPath($file, 'adding to init classes paths');
    $this->classesPaths = array_merge($this->existingClassesPaths, $classesPaths);
    NgnCache::c()->save($this->classesPaths, 'jsClassesPaths');
  }

  protected function parseClassesDefinition($c) {
    if (preg_match_all('/([A-Za-z.]+)\s+=\s+new Class/', $c, $m)) return $m[1];
    return [];
  }

}