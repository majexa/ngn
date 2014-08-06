<?php

class SflmJsClasses {

  /**
   * @var array
   */
  public $existingObjects, $existingObjectPaths, $objectPaths;

  /**
   * @var SflmFrontendJs
   */
  protected $frontend;

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
    return SflmCache::c()->load('jsExistingObjects'.$this->frontend->key());
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
      $classes = $this->parseClassesDefinition(Sflm::getCode($this->frontend->base->getAbsPath($path)));
      $this->existingObjects = array_merge($this->existingObjects, $classes);
    }
    $this->storeExistingObjects();
  }

  protected function storeExistingObjects() {
    if (!$this->existingObjects) Sflm::output('Storing existing objects. Nothing to store. Skipped');
    SflmCache::c()->save([
      $this->existingObjectPaths,
      $this->existingObjects
    ], 'jsExistingObjects'.$this->frontend->key());
  }

  protected function storeExistingObject($name, $source) {
    if (isset($this->existingObjectPaths[$name])) throw new Exception("'$name' already exists ($source)");
    $this->existingObjects[] = $name;
    Sflm::output("Store existing object '$name'");
    $this->storeExistingObjects();
    if ($this->frontend->incrementVersion()) {
      Sflm::output("Increment version on storing object '$name'");
    }
  }

  protected function initObjectPaths() {
    if (($this->objectPaths = SflmCache::c()->load('jsObjectPaths'))) return;
    $this->_initObjectPaths();
  }

  protected function _initObjectPaths() {
    $objectPaths = [];
    $files = [];
    foreach (Sflm::$absBasePaths as $path) $files = Arr::append($files, Dir::getFilesR($path, '[A-Z]*.js'));
    $autoloadFolder = '/js/sflm/';
    foreach ($files as $file) {
      $class = $this->getObjectName($file);
      if (!strstr($file, $autoloadFolder)) continue; // Динамически подключаются только классы находящиеся в папке $autoloadFullSupportFolder
      $objectPaths[$class] = $this->frontend->base->getPath($file, 'adding to init classes paths');
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
    SflmCache::c()->save($this->objectPaths, 'jsObjectPaths');
  }

  protected function parseClassesDefinition($c) {
    if (preg_match_all('/([A-Z][A-Za-z.]+)\s+=\s+new Class/', $c, $m)) return $m[1];
    return [];
  }

  // --

  protected function parseNgnPreloadClasses($c) {
    $r = [];
    if (preg_match_all('/:\s+(Ngn\.[A-Z][A-Za-z.]+)/', $c, $m)) $r = $m[1];
    if (preg_match_all('/:\s+(Ngn\.[A-Za-z]+\.[A-Z][A-Za-z.]+)/', $c, $m)) $r = array_merge($r, $m[1]);
    return $r;
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
    if (in_array($class, $this->existingObjects)) {
      Sflm::output("Class '$class' exists on strict adding (source: $source). Skipped");
      return;
    }
    $this->addObject($class, $source, function () use ($class, $source) {
      throw new Exception($this->captionPrefix($source, $class)." NOT FOUND");
    });
  }

  protected function captionPrefix($source, $name = null) {
    if ($name[strlen($name) - 1] == '.') throw new Exception("incorrect name '$name'");
    return $name ? "object '$name' (src: $source)." : "src: $source";
  }

  protected function namespaceInitExists($code, $namespace) {
    if (preg_match("/$namespace = {}/", $code, $m)) return true;
    return false;
  }

  protected function findObjectPath($name, $strict = true) {
    if (isset($this->objectPaths[$name])) return $this->objectPaths[$name];
    // пытаемся найти в родительских неймспейсах
    foreach ($this->namespaceParents($name) as $parent) {
      if (isset($this->objectPaths[$parent])) return $this->objectPaths[$parent];
    }
    if ($strict) throw new Exception('Object "'.$name.'" path not found');
    return false;
  }

  /**
   * @param string $name Имя класса или объекта
   * @param string $source Описание источника, откуда происходит добавление
   * @param callable $failure
   * @param bool $strict
   * @return bool
   * @throws Exception
   */
  function addObject($name, $source, Closure $failure = null, $strict = true) {
    $ignoreNamespaceParents = false;
    if (($objectPath = $this->findObjectPath($name, false)) === false) {
      $err = "Object '$name' path does not exists. src: $source";
      if (!$strict) {
        Sflm::output($err);
        return false;
      }
      if ($failure) $failure($source);
      else throw new Exception($err);
    }
    // Добавление классов происходит ниже
    if (in_array($name, $this->existingObjects)) {
      Sflm::output('Skip adding '.$this->captionPrefix($source, $name).' EXISTS');
      return false;
    }
    $this->storeExistingObjectsInObjectFile($name, $source);
    if (!$ignoreNamespaceParents and ($namespaceParents = $this->namespaceParents($name))) {
      // Неободимо найти путь к файлу с объектом $name для проверки инициализации родительских неймспейсов в файле объекта
      $objectCode = Sflm::getCode($this->frontend->base->getAbsPath($objectPath));
      // Проверяем всех предков. Подключены ли они. Если вызов происходит не из файла содержащего вероятного родителя
      foreach ($namespaceParents as $parent) {
        // Если неймспейс не найден в файле объекта и его нет в существующих объекта, пытаемся добавить
        if (!$this->namespaceInitExists($objectCode, $parent) and !in_array($parent, $this->existingObjects)) {
          Sflm::output("parent=$parent, source=$source, name=$name");
          $this->addObjectStrict($parent, "[$source] ('$name' parent namespace)");
        }
      }
    }
    $this->_addObject($name, $source);
    $this->_initObjectPaths();
    return true;
  }

  protected function getObjectPath($name, $source, $strcit) {
  }

  protected function _addObject($name, $source) {
    if (!$this->isClass($name)) {
      $this->storeExistingObject($name, 'object adding');
    } // добавляем сразу, т.к. парсинг объявления объектов не поддерживается
    $this->processPath($this->findObjectPath($name), $source, $name);
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
    //if (in_array($path, $this->processedPaths)) die2([$source, $name]);//throw new Exception("Path '$path' already processed. source: $source | <b>$name</b>!");
    Sflm::output("Processing contents of '$path'");
    $this->processedPaths[] = $path;
    $code = Sflm::getCode($this->frontend->base->getAbsPath($path));
    foreach ($this->parseRequired($code) as $class) $this->add($class, "$path required");
    $this->storeExistingObjectsInCode($code);
    $thisCodeNgnObjectsDefinitions = $this->parseNgnObjectsDefinition($code);
    foreach ($this->parseNgnPreloadClasses($code) as $class) {
      if (in_array($class, $thisCodeNgnObjectsDefinitions)) continue;
      //if ($class == 'Ngn.TinyInit.exists') die2([$path, $this->parseNgnPreloadClasses($code)]);
      $this->addObjectStrict($class, ($name ? : $path).' preload');
    }
    Sflm::output('Adding '.($source ? $this->captionPrefix($source, $name).' ' : '')."PATH $path");
    if ($source and isset($this->pathWithSourceProcessor)) {
      $pathWithSourceProcessor = $this->pathWithSourceProcessor;
      $pathWithSourceProcessor($path);
    }
    $this->frontend->_addPath($path);
    $this->processNgnPatterns($code, $path);
    foreach ($this->parseRequiredAfterClasses($code) as $class) $this->add($class, "$path requiredAfter");
  }

  function processCode($code, $source) {
    foreach ($this->parseRequired($code) as $class) $this->add($class, "$source: required");
    foreach ($this->parseNgnPreloadClasses($code) as $class) $this->addObjectStrict($class, "$source: ngn extends");
    $this->processNgnPatterns($code, "$source: ngn patterns");
    foreach ($this->parseRequiredAfterClasses($code) as $class) $this->add($class, "$source: requiredAfter");
  }

  protected function storeExistingObjectsInObjectFile($name) {
    $this->storeExistingObjectsInCode(Sflm::getCode($this->frontend->base->getAbsPath($this->findObjectPath($name))));
  }

  protected function storeExistingObjectsInCode($code) {
    foreach ($this->parseClassesDefinition($code) as $class) {
      if (in_array($class, $this->existingObjects)) {
        continue;
      }
      // Эти классы уже определены
      $this->storeExistingObject($class, 'definition');
    }
  }

  /**
   * @var Closure
   */
  public $pathWithSourceProcessor;

  protected function isObjectOrClass($str) {
    return Misc::firstIsUpper($str);
  }

  function add($str, $source = 'direct') {
    if (!$this->isObjectOrClass($str) and $this->frontend->base->isPackage($str)) {
      foreach ($this->frontend->base->getPaths($str, true) as $path) $this->_add($path, $source);
      return;
    }
    $this->_add($str, $source);
  }

  protected function _add($str, $source) {
    $this->isObjectOrClass($str) ? $this->addObjectStrict($str, $source) : $this->frontend->_addPath($str);
  }

  function parseNgnClasses($c) {
    $classes = [];
    if (preg_match_all('/(Ngn\.[A-Z][A-Za-z._]*[A-Za-z_])/', $c, $m)) {
      $classes = array_filter($m[1], function ($class) {
        return $this->isClass($class);
      });
    }
    if (preg_match_all('/(Ngn\.[A-Za-z]+\.[A-Z][A-Za-z_]*)/', $c, $m)) {
      foreach ($m[1] as $class) if (!in_array($class, $classes)) $classes[] = $class;
    }
    return $classes;
  }

  function parseNgnObjectsDefinition($c) {
    $classes = [];
    if (preg_match_all('/(Ngn\.[A-Z][A-Za-z._]*[A-Za-z_.]+)\s+=\s+/', $c, $m)) {
      $classes = array_filter($m[1], function ($class) {
        return $this->isClass($class);
      });
    }
    return $classes;
  }

  function processNgnPatterns($code, $source = 'default') {
    $code = preg_replace('!/\*.*?\*/!s', '', $code);
    Sflm::output("Process Ngn patterns in '$source'");
    foreach ($this->parseNgnClasses($code) as $class) {
      $this->addObjectStrict($class, "$source « Ngn.Upper*»|« Ngn.anyThing.Upper*» pattern");
    }
  }

}