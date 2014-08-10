<?php

class SflmJsClassPaths extends ArrayAccesseble {

  protected $classes;

  function __construct() {
    if (($this->r = SflmCache::c()->load('jsClassPaths'))) return;
    $this->init();
  }

  protected function init() {
    $this->r = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) {
      $files = Arr::append($files, Dir::getFilesR($path.'/js', '[A-Z]*.js'));
    }
    foreach ($files as $file) {
      $path = Sflm::getPath($file, 'adding to init classes paths');
      foreach (SflmJsClasses::parseValidClassesDefinition(Sflm::getCode($file)) as $class) {
        $this->r[$class] = $path;
      }
    }
    foreach (Ngn::$basePaths as $path) {
      if (($r = Dir::getFilesR($path.'/scripts/js/'))) {
        foreach ($r as $p) {
          $class = basename('s2'.Misc::removePrefix($path.'/scripts', Misc::removeSuffix('.php', $p)));
          if (!SflmJsClasses::isValidClass($class)) continue;
          if (isset($this->r[$class])) throw new Exception("Class '$class' already exists in \$objectPaths. Trying to add path '$p'");
          $this->r[$class] = $p;
        }
      }
    }
    SflmCache::c()->save($this->r, 'jsClassPaths');
  }
  
}