<?php

class SflmJsClassesBase {

  public $existingClasses, $existingClassesPaths, $classesPaths, $frontend;

  function __construct(SflmFrontendJs $frontend) {
    $this->frontend = $frontend;
    $this->initExistingClasses();
    $this->initClassesPaths();
  }

  protected function initExistingClasses() {
    if (($r = FileCache::c()->load('jsExistingClasses'.$this->frontend->frontend))) {
      list($this->existingClassesPaths, $this->existingClasses) = $r;
      return;
    }
    $this->existingClassesPaths = [];
    $this->existingClasses = [];
    $storedPaths = [];
    foreach ($this->frontend->getPaths() as $path) if (preg_match('/.*\/[A-Za-z.]+\.js/', $path)) {
      $storedPaths[] = $path;
    }
    die2($this->frontend->getPaths()) ;
    foreach ($storedPaths as $path) {
      $classes = $this->parseClassesDefinition(file_get_contents($this->frontend->sflm->getAbsPath($path)));
      foreach ($classes as $class) $this->existingClassesPaths[$class] = $path;
      $this->existingClasses = array_merge($this->existingClasses, $classes);
    }
    FileCache::c()->save([$this->existingClassesPaths, $this->existingClasses], 'jsExistingClasses'.$this->frontend->frontend);
  }

  protected function initClassesPaths() {
    if (($this->classesPaths = FileCache::c()->load('jsClassesPaths'))) return;
    $this->_initClassesPaths();
  }

  protected function _initClassesPaths() {
    $classesPaths = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) $files = Arr::append($files, Dir::getFilesR($path, '[A-Z]*.js'));
    foreach ($files as $file) {
      $class = Misc::removeSuffix('.js', basename($file));
      if (!strstr($class, '.')) continue; // Пропускаем корневые классы. Они не подключаются динамически
      $classesPaths[$class] = $this->frontend->sflm->getPath($file, 'adding to init classes paths');
    }
    // -- s2/js/path/to/Ngn.Class.php --
    foreach (Ngn::$basePaths as $path) {
      if (($r = Dir::getFilesR($path.'/scripts/js/', '[A-Z]*'))) {
        foreach ($r as $p) {
          $p = 's2'.Misc::removePrefix($path.'/scripts', Misc::removeSuffix('.php', $p));
          $class = basename($p);
          if (!strstr($class, '.')) continue; // Пропускаем корневые классы. Они не подключаются динамически
          if (isset($classesPaths[$class])) throw new Exception("Class '$class' already exists in \$classesPaths. Trying to add path '$p'");
          $classesPaths[$class] = $p;
        }
      }
    }
    $this->classesPaths = array_merge($this->existingClassesPaths, $classesPaths);
    FileCache::c()->save($this->classesPaths, 'jsClassesPaths');
  }

  protected function parseClassesDefinition($c) {
    if (preg_match_all('/([A-Za-z.]+)\s+=\s+new Class/', $c, $m)) return $m[1];
    return [];
  }

}