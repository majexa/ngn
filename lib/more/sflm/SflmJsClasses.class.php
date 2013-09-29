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
    $this->existingClasses = array_merge($this->existingClasses, $this->parseClassesDefinition(file_get_contents($this->frontend->sflm->getAbsPath($path))));
  }

  protected function parseClassesDefinition($c) {
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

  protected function parseNewNgnClasses($c) {
    if (preg_match_all('/new\s+(Ngn\.[A-Za-z., ]+)/', $c, $m)) return $m[1];
    return [];
  }

  /**
   * @param string JS класс
   * @param string Описание источника, откуда происходит добавление класса
   * @throws Exception
   */
  function addClass($class, $source, Closure $success = null, Closure $failure = null) {
    Sflm::output("Try to add class '$class'. ($source)");
    if (in_array($class, $this->existingClasses)) {
      Sflm::output("class '$class' exists");
      return;
    }
    if (!isset($this->classesPaths[$class])) {
      if ($failure) $failure($source);
      Sflm::output("class '$class' from '$source' not found");
      return false;
    }
    Sflm::output("Adding new class '$class' ($source)");
    $path = $this->classesPaths[$class];
    $this->processPath($path, $success);
    $this->frontend->incrementVersion();
    $this->_initClassesPaths();
    return true;
  }

  /**
   * Должно вызываться уже после добавления пути в фронтенд-библиотеку. Ф-я проверит наличие классов, определенных
   * в в файле по этому пути, добавит их в существующие, а потом проверит все, классы, используемые
   * в этом файле на присутствие. Если какого-то класса не будет среди определённых, то ф-я получит путь
   * к файлу, где лежит этот класс и добавит этот путь во фронтенд-библиотеку
   *
   * @param $path
   */
  function processPath($path) {
    $c = file_get_contents($this->frontend->sflm->getAbsPath($path));
    foreach ($this->parseClassesDefinition($c) as $v) {
      // Эти классы уже определены
      Sflm::output("Class '$v' exists in $path. (definition)");
      $this->existingClasses[] = $v;
    }
    foreach ($this->parseRequired($c) as $v) $this->addSomething($v, "$path required");
    foreach ($this->parseParentClasses($c) as $v) $this->addSomething($v, "$path parent");
    $this->frontend->addLib($path, true);
    $this->processNewNgnClasses($c, $path);
    foreach ($this->parseRequiredAfterClasses($c) as $v) $this->addSomething($v, "$path requiredAfter");
  }

  protected function addSomething($str, $descr = null) {
    Misc::firstIsUpper($str) ? $this->addClass($str, $descr) : $this->frontend->addLib($str);
  }

  function processNewNgnClasses($code, $path = 'default') {
    foreach ($this->parseNewNgnClasses($code) as $v) $this->addClass($v, "$path new");
  }

}