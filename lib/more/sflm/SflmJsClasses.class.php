<?php

class SflmJsClasses extends SflmJsClassesBase {

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
  }

  function parseNewNgnClasses($c) {
    //if (preg_match_all('/\s+(Ngn\.[A-Z][A-Za-z._]+)/', $c, $m)) return $m[1];
    if (preg_match_all('/\s+(Ngn\.[A-Za-z._]+)/', $c, $m)) return $m[1];
    return [];
  }

  /**
   * @param string JS класс
   * @param string Описание источника, откуда происходит добавление класса
   * @throws Exception
   */
  function addClass($class, $source, Closure $success = null, Closure $failure = null) {
    if ($class == 'Ngn.DdGrid.Admin') LogWriter::v('Ngn.DdGrod.Admin', 'trace');
    $prefix = "Try to add class '$class'. ($source). ";
    if (in_array($class, $this->existingClasses)) {
      Sflm::output($prefix."EXISTS");
      return;
    }
    if (!isset($this->classesPaths[$class])) {
      if ($failure) $failure($source);
      Sflm::output($prefix."NOT FOUND");
      return false;
    }
    Sflm::output($prefix."ADDING");
    $path = $this->classesPaths[$class];
    $this->processPath($path, $success);
    $this->frontend->incrementVersion();
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
    output("Processing contents of '$path'");
    $c = file_get_contents($this->frontend->sflm->getAbsPath($path));
    foreach ($this->parseClassesDefinition($c) as $class) {
      // Эти классы уже определены
      Sflm::output("Class '$class' exists in $path. (definition)");
      $this->existingClasses[] = $class;
    }
    foreach ($this->parseRequired($c) as $class) $this->addSomething($class, "$path required");
    foreach ($this->parseParentClasses($c) as $class) $this->addSomething($class, "$path parent");
    $this->frontend->addLib($path, true);
    $this->processNewNgnClasses($c, $path);
    foreach ($this->parseRequiredAfterClasses($c) as $class) $this->addSomething($class, "$path requiredAfter");
  }

  protected function addSomething($str, $descr = null) {
    Misc::firstIsUpper($str) ? $this->addClass($str, $descr) : $this->frontend->addLib($str);
  }

  function processNewNgnClasses($code, $path = 'default') {
    output("processNewNgnClasses of '$path'");
    foreach ($this->parseNewNgnClasses($code) as $class) {
      output("* $class");
      $this->addClass($class, "$path new");
    }
  }

}