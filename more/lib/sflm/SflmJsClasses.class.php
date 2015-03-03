<?php

class SflmNotExists extends Exception {}

class SflmJsClasses {

  /**
   * @var SflmJsFrontendClasses
   */
  public $frontendClasses;

  /**
   * @var SflmJsClassPaths
   */
  public $classPaths;

  /**
   * @var SflmFrontendJs
   */
  protected $frontend;

  /**
   * Включает дополнительные проверки консистентности
   *
   * @var bool
   */
  protected $strict = true;

  function __construct(SflmFrontendJs $frontend, SflmJsClassPaths $classPaths = null) {
    $this->frontend = $frontend;
    $this->classPaths = $classPaths ? : new SflmJsClassPaths($this);
    $this->frontendClasses = new SflmJsFrontendClasses($this->frontend);
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

  /*
  protected function addClassStrict($class, $source) {
    if ($this->frontendClasses->exists($class)) {
      Sflm::output("Class '$class' exists on strict adding. Skipped. src: $source");
      return;
    }
    $this->addClass($class, $source, function () use ($class, $source) {
      throw new Exception(SflmJsClasses::captionPrefix($source, $class)." NOT FOUND");
    });
  }
  */

  protected function namespaceInitExists($code, $namespace) {
    return preg_match("/$namespace = {}/", $code, $m);
  }

  /**
   * @param string $class Имя класса или объекта
   * @param string $source Описание источника, откуда происходит добавление
   * @param callable $failure
   * @param bool $strict
   * @return bool
   * @throws Exception
   */
  function addClass($class, $source, $strict = true) {
    if (!SflmJsClasses::isValidClass($class)) {
      throw new Exception("Class '$class' is not valid. src: $source");
    }
    if (!isset($this->classPaths[$class])) {
      $err = "Class '$class' does not exists. src: $source";
      if (!$strict) {
        Sflm::log($err);
        return false;
      }
      throw new SflmNotExists($err);
    }
    if ($this->frontendClasses->exists($class)) {
      Sflm::log("Class '$class' exists. Skipped. src: $source");
      return false;
    }
    $this->frontendClasses->add($class, $source);
    $this->processNamespaceParents($class, $source);
    $this->processPath($this->classPaths[$class], $source, $class);
    return true;
  }

  function addSomething($something, $source) {
    strstr($something, '/') ? $this->processPath($something, $source) : $this->addClass($something, $source);
  }

  protected function processNamespaceParents($class, $source) {
    if (!($namespaceParents = $this->namespaceParents($class))) return;
    $code = Sflm::getCode($this->frontend->base->getAbsPath($this->classPaths[$class]));
    // Проверяем всех предков, подключены ли они
    foreach ($namespaceParents as $parent) {
      // Если неймспейс не найден в файле объекта и его нет в существующих объектах, то пытаемся добавить
      if (!$this->namespaceInitExists($code, $parent) and !$this->frontendClasses->exists($parent)) {
        $this->addClass($parent, "[$source] ($class parent namespace)");
      }
    }
  }

  protected $processedPaths = [];

  /**
   * Должно вызываться уже после добавления пути в фронтенд-библиотеку. Ф-я проверит наличие классов, определенных
   * в файле по этому пути, добавит их в существующие, а потом проверит все классы, используемые
   * в этом файле на присутствие. Если какого-то класса не будет среди определённых, то ф-я получит путь
   * к файлу, где лежит этот класс, и добавит этот путь во фронтенд-библиотеку
   *
   * @param string $path Путь к файлу
   * @param string|null $source Описание источника, откуда происходит вызов обработки пути
   * @param string|null $name Имя объекта/класса который должен находиться по этому пути
   * @throws Exception
   */
  function processPath($path, $source = null, $name = null) {
    if (Misc::hasSuffix('', $path))
    if (in_array($path, $this->frontend->pathsCache)) {
      Sflm::log("Path '$path' in cache. Skipped");
      return;
    }
    if (in_array($path, $this->processedPaths)) throw new Exception("Path '$path' already processed. src: $source | $name!");
    Sflm::log("Processing contents of '$path'");
    $this->processedPaths[] = $path;
    $code = Sflm::getCode($this->frontend->base->getAbsPath($path));
    $this->processCode($code, $path, $name, $path);
  }

  function processCode($code, $source, $name = null, $path = null) {
    $this->frontendClasses->processCode($code, $source); // ------ добавили класс
    $thisCodeValidClassesDefinition = SflmJsClasses::parseValidClassesDefinition($code);
    foreach (SflmJsClasses::parseValidPreloadClasses($code) as $class) {
      if (in_array($class, $thisCodeValidClassesDefinition)) continue;
      $this->addClass($class, ($name ? : $path ? : '').' preload');
    }
    foreach (SflmJsClasses::parseRequired($code, 'before') as $class) {
      $this->addSomething($class, "$path requiredBefore");
    }
    Sflm::log('Adding '.($source ? SflmJsClasses::captionPrefix($source, $name).' ' : '').($path ? "PATH $path" : 'CODE'));
    if ($path) $this->frontend->_addPath($path); // -------------- добавили путь (!)
    Sflm::log("Processing valid-class patterns in '$source'");
    foreach (SflmJsClasses::parseValidClassesUsage(Sflm::stripComments($code)) as $class) {
      if (in_array($class, $thisCodeValidClassesDefinition)) continue;
      $this->addClass($class, "$source valid-class pattern");
    }
    foreach (SflmJsClasses::parseRequired($code, 'after') as $class) {
      $this->addSomething($class, "$path requiredAfter");
    }
    // the same as previous
    foreach (SflmJsClasses::parseRequired($code) as $class) {
      $this->addSomething($class, "$path requiredAfter");
    }
  }

  function addFrontendClass($name, $source) {
    $path = $this->classPaths[$name];
    $this->frontendClasses->processCode(Sflm::getCode($this->frontend->base->getAbsPath($path)), $path);
  }

  // --

  static function validName($name) {
    return $name[strlen($name) - 1] != '.';
  }

  static function isClass($class) {
    $parts = explode('.', $class);
    if (count($parts) > 1 and !Misc::firstIsUpper($parts[count($parts) - 1])) return false;
    return true;
  }

  static function isValidClass($class) {
    if (!Misc::hasPrefix('Ngn.', $class)) return false;
    if (!SflmJsClasses::validName($class)) return false;
    if (!SflmJsClasses::isClass($class)) return false;
    return true;
  }

  static function isValidClassMethod($class) {
    if (!Misc::hasPrefix('Ngn.', $class)) return false;
    if (!SflmJsClasses::validName($class)) return false;
    return true;
  }

  static function cutClassMethod($class) {
    return preg_replace('/(.*)\.[A-Za-z_]+/', '$1', $class);
  }

  static function parseValidClasses($code, $prefix = '', $suffix = '') {
    $classes = [];
    if (preg_match_all('/'.$prefix.'(Ngn\.[A-Za-z.]+)'.$suffix.'/', $code, $m)) {
      foreach ($m[1] as $piece) {
        if (in_array($piece, $classes)) continue;
        if (!SflmJsClasses::isValidClass($piece)) continue;
        $classes[] = $piece;
      }
    }
    return $classes;
  }

  static protected function parseValidClassesUsage($code) {
    $classes = [];
    if (preg_match_all('/(Ngn\.[A-Za-z.]+)/', $code, $m)) {
      foreach ($m[1] as $piece) {
        if (in_array($piece, $classes)) continue;
        if (!SflmJsClasses::isValidClass($piece)) {
          if (SflmJsClasses::isValidClassMethod($piece)) {
            $class = SflmJsClasses::cutClassMethod($piece);
            if (!SflmJsClasses::isValidClass($class) or in_array($class, $classes)) continue;
            $classes[] = $class;
          }
          continue;
        }
        $classes[] = $piece;
      }
    }
    return $classes;
  }

  static function parseValidClassesDefinition($code) {
    return SflmJsClasses::parseValidClasses($code, '', '\s+=\s+');
  }

  static function parseValidPreloadClasses($code) {
    return SflmJsClasses::parseValidClasses($code, '[A-Za-z]:\s+');
  }

  static function parseRequired($code, $k = '') {
    $r = [];
    if (preg_match_all('/@requires'.ucfirst($k).'\s+([0-9?=&A-Za-z.\\/, ]+)/', $code, $m)) {
      foreach ($m[1] as $v) $r = array_merge($r, array_map('trim', explode(',', $v)));
    }
    return $r;
  }

  static function captionPrefix($source, $name = null) {
    return $name ? "class '$name' (src: $source)." : "src: $source";
  }

}