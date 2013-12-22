<?php

class SflmJsClass extends SflmJsClassesBase {
use Options;

  protected function parseParentClass($class, $c) {
    $class = str_replace('.', '\\.', $class);
    if (preg_match('/'.$class.'\s+=\s+new\s+Class\(\{\s+Extends:\s+([A-Z][A-Za-z.]+)/ms', $c, $m)) return $m[1];
    return false;
  }

  function parent($class) {
    if (!isset($this->classesPaths[$class])) throw new Exception("Path for class {$class} does not exists");
    $c = file_get_contents($this->frontend->sflm->getAbsPath($this->classesPaths[$class]));
    if (!($parentClass = $this->parseParentClass($class, $c))) return false;
    return $parentClass;
  }

  function parents($class) {
    $parents = [];
    while (($class = $this->parent($class))) $parents[] = $class;
    return $parents;
  }

}