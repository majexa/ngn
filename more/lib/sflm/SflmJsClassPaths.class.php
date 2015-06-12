<?php

class SflmJsClassPaths extends ArrayAccesseble {

  protected $classes;

  function __construct() {
    if (($this->r = SflmCache::c()->load('sflmJsClassPaths'))) return;
    $this->init();
  }

  protected function files() {
    $files = [];
    foreach (Sflm::$absBasePaths as $path) {
      $files = array_merge($files, Dir::getFilesR($path.'/js', '[A-Z]*.js'));
    }
    return $files;
  }

  protected function init() {
    $this->r = [];
    foreach ($this->files() as $file) {
      $path = Sflm::getPath($file, 'adding to init classes paths');
      foreach (SflmJsClasses::parseValidClassesDefinition(Sflm::getCode($file)) as $class) {
        $this->r[$class] = $path;
      }
    }
    foreach (Ngn::$basePaths as $path) {
      if (($r = Dir::getFilesR($path.'/scripts/js/'))) {
        foreach ($r as $p) {
          $p = 's2'.Misc::removePrefix($path.'/scripts', $p);
          $class = basename(Misc::removeSuffix('.php', $p));
          if (!SflmJsClasses::isValidClass($class)) continue;
          if (isset($this->r[$class])) throw new Exception("Class '$class' already exists in \$objectPaths. Trying to add path '$p'");
          $this->r[$class] = $p;
        }
      }
    }
    SflmCache::c()->save($this->r, 'sflmJsClassPaths');
  }

  function fullPath() {

  }

}