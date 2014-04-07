<?php

class SflmJsClasses {

  /**
   * @var
   */
  public $existingObjects;

  public $existingObjectPaths, $objectPaths, $frontend;

  function __construct(SflmFrontendJs $frontend) {
    $this->frontend = $frontend;
    $this->initExistingObjects();
    $this->initObjectPaths();
  }

  protected function isObjectPath($path) {
    return preg_match('/.*\/[A-Za-z.]+\.js/', $path);
  }

  protected function getObjectName($path) {
    return Misc::removeSuffix('.js', basename($path));
  }

  function retrieveExistingObjects() {
    return FileCache::c()->load('jsExistingObjects'.$this->frontend->fKey());
  }

  protected function initExistingObjects() {
    if (($r = $this->retrieveExistingObjects())) {
      list($this->existingObjectPaths, $this->existingObjects) = $r;
      return;
    }
    $this->existingObjectPaths = [];
    $this->existingObjects = [];
    $storedPaths = [];
    foreach ($this->frontend->getPaths() as $path) if ($this->isObjectPath($path)) $storedPaths[] = $path;
    foreach ($storedPaths as $path) {
      $objectName = $this->getObjectName($path);
      if (!$this->isClass($objectName)) {
        $this->existingObjectPaths[$objectName] = $path;
        $this->existingObjects[] = $objectName;
        return;
      }
      $classes = $this->parseClassesDefinition(file_get_contents($this->frontend->sflm->getAbsPath($path)));
      foreach ($classes as $className) $this->existingObjectPaths[$className] = $path;
      $this->existingObjects = array_merge($this->existingObjects, $classes);
    }
    $this->storeExistingObjects();
  }

  protected function storeExistingObjects() {
    if (!$this->existingObjects) {
      Sflm::output('Storing existing objects. Nothing to store. Skipped');
      return;
    }
    //Sflm::output('Storing existing objects: '.implode(', ', $this->existingObjects)." to jsExistingObjects".$this->frontend->fKey());
    Sflm::output('Storing existing objects. Count: '.count($this->existingObjects));
    FileCache::c()->save([
      $this->existingObjectPaths,
      $this->existingObjects
    ], 'jsExistingObjects'.$this->frontend->fKey());
  }

  protected function storeExistingObject($name, $source) {
    if (isset($this->existingObjectPaths[$name])) throw new Exception("'$name' already exists ($source)");
    $this->existingObjects[] = $name;
    $this->storeExistingObjects();
    if ($this->frontend->incrementVersion()) {
      Sflm::output("Increment version on storing object '$name'");
    }
  }

  protected function initObjectPaths() {
    if (($this->objectPaths = FileCache::c()->load('jsObjectPaths'))) return;
    $this->_initObjectPaths();
  }

  protected function _initObjectPaths() {
    $objectPaths = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) $files = Arr::append($files, Dir::getFilesR($path, '[A-Z]*.js'));
    foreach ($files as $file) {
      $class = $this->getObjectName($file);
      if (!strstr($class, '.')) continue; // Пропускаем корневые классы. Они не подключаются динамически
      $objectPaths[$class] = $this->frontend->sflm->getPath($file, 'adding to init classes paths');
    }
    // -- s2/js/path/to/Ngn.Class.php --
    foreach (Ngn::$basePaths as $path) {
      if (($r = Dir::getFilesR($path.'/scripts/js/', '[A-Z]*'))) {
        foreach ($r as $p) {
          $p = 's2'.Misc::removePrefix($path.'/scripts', Misc::removeSuffix('.php', $p));
          $class = basename($p);
          if (!strstr($class, '.')) continue; // Пропускаем корневые классы. Они не подключаются динамически
          if (isset($objectPaths[$class])) throw new Exception("Class '$class' already exists in \$objectPaths. Trying to add path '$p'");
          $objectPaths[$class] = $p;
        }
      }
    }
    $this->objectPaths = array_merge($this->existingObjectPaths, $objectPaths);
    FileCache::c()->save($this->objectPaths, 'jsObjectPaths');
  }

  protected function parseClassesDefinition($c) {
    if (preg_match_all('/([A-Z][A-Za-z.]+)\s+=\s+new Class/', $c, $m)) return $m[1];
    return [];
  }

  // --

  protected function parseNgnExtendsClasses($c) {
    if (preg_match_all('/Extends:\s+(Ngn\.[A-Za-z.]+)/', $c, $m)) return $m[1];
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

  protected function isClass($name) {
    $parts = explode('.', $name);
    if (count($parts) > 1 and !Misc::firstIsUpper($parts[count($parts) - 1])) return false;
    return true;
  }

  protected function addObjectStrict($class, $source) {
    $this->addObject($class, $source, function () use ($class, $source) {
      throw new Exception($this->captionPrefix($source, $class)." NOT FOUND");
    });
  }

  protected function captionPrefix($source, $name = null) {
    return $name ? "object '$name' (src: $source)." : "src: $source";
  }

  /**
   * Добавляет к фронтенду класс или объект
   *
   * @param string Имя класса или объекта
   * @param string Описание источника, откуда происходит добавление класса
   * @throws Exception
   */
  function addObject($name, $source, Closure $failure = null, Closure $success = null, $ignoreNamespaceParents = false) {
    // Добавление классов происходит ниже
    if (in_array($name, $this->existingObjects)) {
      Sflm::output('Skip adding '.$this->captionPrefix($source, $name).' EXISTS');
      return false;
    }
    if (!$ignoreNamespaceParents and ($namespaceParents = $this->namespaceParents($name))) {
      // Проверяем всех предков. Подключены ли они. Если вызов происходит не из файла содержащего вероятного родителя
      foreach ($namespaceParents as $parent) {
        if (!in_array($parent, $this->existingObjects)) {
          $this->addObjectStrict($parent, "[$source] ('$name' parent namespace)");
        }
      }
    }
    if (in_array($name, $this->existingObjects)) {
      Sflm::output('Skip adding '.$this->captionPrefix($source, $name).' EXISTS AFTER PARSING NAMESPACE PARENTS');
      return false;
    }
    $this->_addObject($name, $source, $failure);
    $this->_initObjectPaths();
    return true;
  }

  protected function getObjectPath($name, $source, $strcit) {
  }

  protected function _addObject($name, $source, Closure $failure) {
    if (!isset($this->objectPaths[$name])) {
      if ($failure) $failure($source);
      return;
    }
    if (!$this->isClass($name)) {
      $this->storeExistingObject($name, 'object adding');
    } // добавляем сразу, т.к. парсинг объявления объектов не поддерживается
    $this->processPath($this->objectPaths[$name], $source, $name);
  }

  /**
   * Должно вызываться уже после добавления пути в фронтенд-библиотеку. Ф-я проверит наличие классов, определенных
   * в файле по этому пути, добавит их в существующие, а потом проверит все классы, используемые
   * в этом файле на присутствие. Если какого-то класса не будет среди определённых, то ф-я получит путь
   * к файлу, где лежит этот класс, и добавит этот путь во фронтенд-библиотеку
   *
   * @param $path
   */
  function processPath($path, $source = null, $name = null) {
    //LogWriter::str('sflm', $path);
    Sflm::output("Processing contents of '$path'");
    $c = file_get_contents($this->frontend->sflm->getAbsPath($path));
    foreach ($this->parseClassesDefinition($c) as $class) {
      if (in_array($class, $this->existingObjects)) continue;
      // Эти классы уже определены
      Sflm::output("Class '$class' exists in $path. [definition] (src: $source)");
      $this->storeExistingObject($class, 'definition');
    }
    foreach ($this->parseRequired($c) as $class) $this->add($class, "$path required");
    foreach ($this->parseNgnExtendsClasses($c) as $class) $this->addObjectStrict($class, ($name ? : $path).' extends');
    Sflm::output('Adding '.($source ? $this->captionPrefix($source, $name).' ' : '')."PATH $path");
    if ($source and isset($this->pathWithSourceProcessor)) {
      $pathWithSourceProcessor = $this->pathWithSourceProcessor;
      $pathWithSourceProcessor($path);
    }
    $this->frontend->_addPath($path, true);
    $this->processNgnClasses($c, $path);
    foreach ($this->parseRequiredAfterClasses($c) as $class) $this->add($class, "$path requiredAfter");
  }

  /**
   * @var Closure
   */
  public $pathWithSourceProcessor;

  protected function isObjectOrClass($str) {
    return Misc::firstIsUpper($str);
  }

  function add($str, $source = 'direct') {
    if (!$this->isObjectOrClass($str) and $this->frontend->sflm->isPackage($str)) throw new Exception("Path '$str' can not be a package. src: $source");
    $this->isObjectOrClass($str) ? $this->addObjectStrict($str, $source) : $this->frontend->_addPath($str);
  }

  function parseNgnClasses($c) {
    $classes = [];
    if (preg_match_all('/\s+(Ngn\.[A-Z][A-Za-z._]+)/', $c, $m)) {
      $classes = array_filter($m[1], function ($class) {
        return $this->isClass($class);
      });
    }
    if (preg_match_all('/\s+(Ngn\.[A-Za-z]+\.[A-Z][A-Za-z_]*)/', $c, $m)) {
      foreach ($m[1] as $class) if (!in_array($class, $classes)) $classes[] = $class;
    }
    return $classes;
  }

  function processNgnClasses($code, $path = 'default') {
    //die2('!');
    $code = preg_replace('!/\*.*?\*/!s', '', $code);
    Sflm::output("Process « Ngn.[Upper]*» patterns by path '$path'");
    foreach ($this->parseNgnClasses($code) as $class) {
      $this->addObjectStrict($class, "$path ' Ngn.Upper*' pattern");
    }
  }

}