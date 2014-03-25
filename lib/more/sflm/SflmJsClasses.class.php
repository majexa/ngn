<?php

class SflmJsClasses extends SflmJsClassesBase {

  public $existingObjects, $existingObjectPaths, $objectPaths, $frontend;

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

  protected function initExistingObjects() {
    if (($r = FileCache::c()->load('jsExistingObjects'.$this->frontend->frontend))) {
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
    FileCache::c()->save([$this->existingObjectPaths, $this->existingObjects], 'jsExistingObjects'.$this->frontend->frontend);
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
    if (preg_match_all('/([A-Za-z.]+)\s+=\s+new Class/', $c, $m)) return $m[1];
    return [];
  }

  // --

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

  protected function isClass($name) {
    $parts = explode('.', $name);
    if (count($parts) > 1 and !Misc::firstIsUpper($parts[count($parts)-1])) return false;
    return true;
  }

  protected function addObjectStrict($class, $source) {
    $this->addObject($class, $source, null, function() use ($class, $source) {
      throw new Exception($this->captionPrefix($class, $source)."NOT FOUND");
    });
  }

  protected function captionPrefix($name, $source) {
    return "Try to add object '$name'. ($source). ";
  }

  /**
   * Добавляет к фронтенду класс или объект
   *
   * @param string Имя класса или объекта
   * @param string Описание источника, откуда происходит добавление класса
   * @throws Exception
   */
  function addObject($name, $source, Closure $success = null, Closure $failure = null, $ignoreNamespaceParents = false) {
    $prefix = $this->captionPrefix($name, $source);
    // Добавление классов происходит ниже
    if (in_array($name, $this->existingObjects)) {
      Sflm::output($prefix."EXISTS");
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
      Sflm::output($prefix."EXISTS AFTER PARSING NAMESPACE PARENTS");
      return false;
    }
    if (!isset($this->objectPaths[$name])) {
      if ($failure) $failure($source);
      Sflm::output($prefix."NOT FOUND");
      return false;
    }
    Sflm::output($prefix."ADDING");
    $this->_addObject($name, $success);
    if ($this->frontend->incrementVersion()) {
      Sflm::output("Increment version on adding '$name' class from $source");
    }
    $this->_initObjectPaths();
    return true;
  }

  protected function _addObject($class, $success) {
    if (!$this->isClass($class)) $this->existingObjects[] = $class; // добавляем сразу, т.к. парсинг объявления объектов не поддерживается
    $this->processPath($this->objectPaths[$class], $success);
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
      if (in_array($class, $this->existingObjects)) continue;
      // Эти классы уже определены
      Sflm::output("Class '$class' exists in $path. (definition)");
      $this->existingObjects[] = $class;
    }
    foreach ($this->parseRequired($c) as $class) $this->addSomething($class, "$path required");
    foreach ($this->parseNgnExtendsClasses($c) as $class) $this->addObjectStrict($class, "$path extends");
    $this->frontend->addLib($path, true);
    $this->processNgnClasses($c, $path);
    foreach ($this->parseRequiredAfterClasses($c) as $class) $this->addSomething($class, "$path requiredAfter");
  }

  protected function isObjectOrClass($str) {
    return Misc::firstIsUpper($str);
  }

  function addSomething($str, $descr = null) {
    $this->isObjectOrClass($str) ? $this->addObjectStrict($str, $descr) : $this->frontend->addLib($str);
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
    Sflm::output("Process ' Ngn.[Upper]*' patterns by path '$path'");
    foreach ($this->parseNgnClasses($code) as $class) {
      $this->addObjectStrict($class, "$path ' Ngn.Upper*' pattern");
    }
  }

}