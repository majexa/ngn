<?php

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

  function __construct(SflmFrontendJs $frontend) {
    $this->frontend = $frontend;
    $this->classPaths = new SflmJsClassPaths($this);
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
      throw new Exception("Class '$class' is not valid");
    }
    if (!isset($this->classPaths[$class])) {
      $err = "Class '$class' path does not exists. src: $source";
      if (!$strict) {
        Sflm::output($err);
        return false;
      }
      throw new Exception($err);
    }
    if ($this->frontendClasses->exists($class)) {
      Sflm::output("Class '$class' exists. Skipped. src: $source");
      return false;
    }
    $this->frontendClasses->add($class, $source);
    $this->processNamespaceParents($class, $source);
    $this->processPath($this->classPaths[$class], $source, $class);
    return true;
  }

  protected function processNamespaceParents($class, $source) {
    if (!($namespaceParents = $this->namespaceParents($class))) return;
    $code = Sflm::getCode($this->frontend->base->getAbsPath($this->classPaths[$class]));
    // Проверяем всех предков, подключены ли они
    foreach ($namespaceParents as $parent) {
      // Если неймспейс не найден в файле объекта и его нет в существующих объекта, пытаемся добавить
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
    if (in_array($path, $this->frontend->pathsCache)) {
      Sflm::output("Path '$path' in cache. Skipped");
      return;
    }
    if (in_array($path, $this->processedPaths)) throw new Exception("Path '$path' already processed. src: $source | $name!");
    Sflm::output("Processing contents of '$path'");
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
    Sflm::output('Adding '.($source ? SflmJsClasses::captionPrefix($source, $name).' ' : '').($path ? "PATH $path" : 'CODE'));
    if ($path) $this->frontend->_addPath($path); // -------------- добавили путь
    Sflm::output("Processing valid-class patterns in '$source'");
    foreach (SflmJsClasses::parseValidClasses($code) as $class) {
      $this->addClass($class, "$source valid-class pattern");
    }
    foreach (SflmJsClasses::parseRequiredAfterClasses($code) as $class) {
      $this->addClass($class, "$path requiredAfter");
    }
  }

  function addFrontendClass($name, $source) {
    $path = $this->classPaths[$name];
    //outputColor('Store existing objects in object "'.$name.'" file "'.$path.'" FROM '.$source, 'brown');
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
    return preg_replace('/$(.*)\.[A-Za-z_]^/', '$1', $class);
  }

  static protected function parseValidClasses($c, $prefix = '', $suffix = '') {
    $classes = [];
    if (preg_match_all('/'.$prefix.'(Ngn\.[A-Za-z.]+)'.$suffix.'/', $c, $m)) {
      foreach ($m[1] as $piece) {
        if (in_array($piece, $classes)) continue;
        if (!SflmJsClasses::isValidClass($piece)) {
          //if (SflmJsClasses::isValidClassMethod($piece)) {
          //  $classes[] = SflmJsClasses::cutClassMethod($piece);;
          //}
          continue;
        }
        $classes[] = $piece;
      }
    }
    return $classes;
  }

  static function parseValidClassesDefinition($c) {
    return SflmJsClasses::parseValidClasses($c, '', '\s+=\s+');
  }

  static function parseValidPreloadClasses($c) {
    return SflmJsClasses::parseValidClasses($c, '[A-Za-z]:\s+');
  }

  static function parseRequired($c, $k = '') {
    $r = [];
    if (preg_match_all('/@requires'.ucfirst($k).'\s+([A-Za-z., ]+)/', $c, $m)) {
      foreach ($m[1] as $v) $r = array_merge($r, array_map('trim', explode(',', $v)));
    }
    return $r;
  }

  static function parseRequiredAfterClasses($c) {
    return SflmJsClasses::parseRequired($c, 'after');
  }

  static function captionPrefix($source, $name = null) {
    return $name ? "class '$name' (src: $source)." : "src: $source";
  }

}