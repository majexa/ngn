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
    $this->_initClassesPaths();
  }

  protected function _initClassesPaths() {
    $this->classesPaths = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) $files = Arr::append($files, Dir::getFilesR($path, '[A-Z]*.js'));
    //foreach (Ngn::$basePaths as $path) $files = Arr::append($files, Dir::getFilesR($path.'/scripts/js', '[A-Z]*'));
    foreach ($files as $file) $this->classesPaths[Misc::removeSuffix('.js', basename($file))] = $this->frontend->sflm->getPath($file, 'adding to init classes paths');
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

  protected function parseRequired($c, $k = '') {
    $r = [];
    if (preg_match_all('/@requires'.ucfirst($k).'\s+([A-Za-z., ]+)/', $c, $m)) {
      foreach ($m[1] as $v) $r = array_merge($r, array_map('trim', explode(',', $v)));
    }
    return $r;
  }

  protected function parseRequiredAfterClasses($c) {
    return $this->parseRequired($c, 'after');
    // does not work properly
    //if (preg_match_all('/new\s+([A-Z][A-Za-z.]+)/', $c, $m)) {
      //foreach ($m[1] as $class) if ($class != 'Class') $r[] = $class;
    //}
  }

  /**
   * @param JS класс
   * @param Текстовое описание источника, откуда происходит добавление класс
   * @throws Exception
   */
  function addClass($class, $source, Closure $success = null, Closure $failure = null) {
    //Sflm::output("try to addClass '$class'");
    if (in_array($class, $this->existingClasses)) {
      //Sflm::output("class '$class' exists");
      return;
    }
    if (!isset($this->classesPaths[$class])) {
      if ($failure) $failure($source);
      Sflm::output("class '$class' from '$source' not found");
      //pr("class '$class' from '$source' not found");
      return false;
    }
    Sflm::output("addClass '$class'");
    $path = $this->classesPaths[$class];
    $this->processPath($path, $success);
    $this->frontend->incrementVersion();
    $this->_initClassesPaths();
    return true;
  }

  function processPath($path) {
    $c = file_get_contents($this->frontend->sflm->getAbsPath($path));
    foreach ($this->parseClasses($c) as $v) {
      Sflm::output("Class '$v' exists in $path. Adding to \$this->existingClasses");
      $this->existingClasses[] = $v;
    }
    foreach ($this->parseRequired($c) as $v) $this->addSomething($v, "$path required");
    foreach ($this->parseParentClasses($c) as $v) $this->addSomething($v, "$path parent");
    $this->frontend->addLib($path, true);
    foreach ($this->parseRequiredAfterClasses($c) as $v) $this->addSomething($v, "$path requiredAfter");
    //NgnCache::c()->save($this->existingClasses, 'jsExistingClasses');
  }

  protected function addSomething($str, $descr = null) {
    Misc::firstIsUpper($str) ? $this->addClass($str, $descr) : $this->frontend->addLib($str);
  }

}