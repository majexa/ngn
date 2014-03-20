<?php

class SflmJsClasses extends SflmJsClassesBase {

  protected function parseNgnExtendsClasses($c) {
    if (preg_match_all('/Extends:\s+(Ngn\.[A-Z][A-Za-z.]+)/', $c, $m)) return $m[1];
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
  }

  protected function namespaceParents($class) {
    if (substr_count($class, '.') < 2) return [];
    $parents = explode('.', $class);
    $r = [];
    for ($i = 2; $i < count($parents); $i++) {
      $r[] = implode('.', array_slice($parents, 0, $i));
    }
    return $r;
  }

  protected function isClass($class) {
    $parts = explode('.', $class);
    if (count($parts) > 1 and !Misc::firstIsUpper($parts[count($parts)-1])) return false;
    return true;
  }

  protected function addClassStrict($class, $source) {
    $this->addClass($class, $source, null, function() use ($class, $source) {
      throw new Exception($this->captionPrefix($class, $source)."NOT FOUND");
    });
  }

  protected function captionPrefix($class, $source) {
    return "Try to add class '$class'. ($source). ";
  }

  /**
   * @param string JS класс
   * @param string Описание источника, откуда происходит добавление класса
   * @throws Exception
   */
  function addClass($class, $source, Closure $success = null, Closure $failure = null, $ignoreNamespaceParents = false) {
    $prefix = $this->captionPrefix($class, $source);
    if (!$this->isClass($class)) throw new Exception("'$class' is not class. Skipped");
    if (in_array($class, $this->existingClasses)) {
      Sflm::output($prefix."EXISTS");
      return false;
    }
    if (!$ignoreNamespaceParents and ($namespaceParents = $this->namespaceParents($class))) {
      // Проверяем всех предков. Подключены ли они. Если вызов происходит не из файла содержащего вероятного родителя
      foreach ($namespaceParents as $parent) {
        if (!in_array($parent, $this->existingClasses)) {
          $this->addClassStrict($parent, "'$class' parent namespace");
        }
      }
    }
    if (in_array($class, $this->existingClasses)) {
      Sflm::output($prefix."EXISTS AFTER PARSING NAMESPACE PARENTS");
      return false;
    }
    if (!isset($this->classesPaths[$class])) {
      if ($failure) $failure($source);
      Sflm::output($prefix."NOT FOUND");
      return false;
    }
    Sflm::output($prefix."ADDING");
    $path = $this->classesPaths[$class];
    $this->processPath($path, $success);
    if ($this->frontend->incrementVersion()) {
      Sflm::output("Increment version on adding '$class' class from $source");
    }
    $this->_initClassesPaths();
    return true;
  }

  /**
   * Должно вызываться уже после добавления пути в фронтенд-библиотеку. Ф-я проверит наличие классов, определенных
   * в файле по этому пути, добавит их в существующие, а потом проверит все классы, используемые
   * в этом файле на присутствие. Если какого-то класса не будет среди определённых, то ф-я получит путь
   * к файлу, где лежит этот класс, и добавит этот путь во фронтенд-библиотеку
   *
   * @param $path
   */
  function processPath($path) {
    Sflm::output("Processing contents of '$path'");
    $c = file_get_contents($this->frontend->sflm->getAbsPath($path));
    foreach ($this->parseClassesDefinition($c) as $class) {
      if (in_array($class, $this->existingClasses)) continue;
      // Эти классы уже определены
      Sflm::output("Class '$class' exists in $path. (definition)");
      $this->existingClasses[] = $class;
    }
    foreach ($this->parseRequired($c) as $class) $this->addSomething($class, "$path required");
    foreach ($this->parseNgnExtendsClasses($c) as $class) $this->addClassStrict($class, "$path extends");
    $this->frontend->addLib($path, true);
    $this->processNgnClasses($c, $path);
    foreach ($this->parseRequiredAfterClasses($c) as $class) $this->addSomething($class, "$path requiredAfter");
  }

  function addSomething($str, $descr = null) {
    Misc::firstIsUpper($str) ? $this->addClassStrict($str, $descr) : $this->frontend->addLib($str);
  }

  function parseNgnClasses($c) {
    if (preg_match_all('/\s+(Ngn\.[A-Z][A-Za-z._]+)/', $c, $m)) {
      return array_filter($m[1], function($class) {
        return $this->isClass($class);
      });
    }
    return [];
  }

  function processNgnClasses($code, $path = 'default') {
    Sflm::output("Process ' Ngn.[Upper]*' patterns by '$path'");
    foreach ($this->parseNgnClasses($code) as $class) {
      $this->addClassStrict($class, "$path ' Ngn.Upper*' pattern");
    }
  }

}