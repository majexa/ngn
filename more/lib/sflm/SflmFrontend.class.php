<?php

/**
 * Коллекцианирует и кэширует пути к статическим файлам во время выполнения приложения
 */
abstract class SflmFrontend {

  /**
   * @var string Имя фронтенда
   */
  public $name;

  protected $pathsCache, $id, $debug = false;

  public $base, $newPaths = [];

  function __construct(SflmBase $base, $name) {
    $this->id = Misc::randString(5);
    $this->base = $base;
    $this->name = $name;
    Misc::checkEmpty($this->name, '$this->frontend');
    $this->base->version = $this->version();
    $this->pathsCache = $this->getPathsCache() ? : $this->base->getPaths($this->name, true);
    //if ($this->getLastPaths() != $this->getPaths()) {
    //  $this->incrementVersion();
    //  $this->storeLastPaths();
    //}
    $this->init();
  }

  /**
   * Возвращает статические (SflmBase) и динамические (SflmFrontend) пути вместе
   */
  function getPaths() {
    return $this->pathsCache;
  }

  protected function cacheSuffix() {
    return $this->base->type.$this->fKey();
  }

  function fKey() {
    return str_replace('/', '_', $this->name);
  }

  protected function getLastPaths() {
    return FileCache::c()->load('sflmLastPaths'.$this->cacheSuffix()) ? : [];
  }

  protected function storeLastPaths() {
    FileCache::c()->save($this->getPaths(), 'sflmLastPaths'.$this->cacheSuffix());
  }

  /**
   * Возвращает сохраненные для текущего фронтенда runtime пути
   *
   * @return array
   */
  function getPathsCache() {
    return FileCache::c()->load($this->pathsCacheKey()) ? : [];
  }

  protected function init() {
  }

  public $extraCode = "\n//***";

  function code() {
    if ($this->stored) throw new Exception("Can't get code after frontend was stored. Reset or rerun frontend");
    return $this->_code();
  }

  function _code() {
    return $this->base->extractCode($this->getPaths());
  }

  function getTags() {
    $this->store('SflmFrontend::getTags');
    return $this->base->getTags($this->name, $this->_code());
  }

  function getTag() {
    return $this->base->getTag($this->name);
  }

  protected $stored = false;

  function store($source = 'direct') {
    if ($this->stored) throw new Exception("Can't store after frontend was already stored. Reset or rerun frontend");
    if (!$this->changed) {
      Sflm::output("No changes. Storing skipped");
      return;
    }
    Sflm::output("Update collected '{$this->name}.{$this->base->type}' file after adding lib ".($source ? "from '$source' source" : ''));
    $this->storePaths();
    //if (!$this->sflm->storeLib($this->name, $this->code())) throw new Exception('it should not have happened');
    if ($this->base->storeLib($this->name, $this->code())) {
      $this->incrementVersion();
      $this->stored = true;
    }
  }

  function storePaths() {
    FileCache::c()->save($this->getPaths(), $this->pathsCacheKey());
  }

  function filePath() {
    return $this->base->filePath($this->name);
  }

  function cacheFile() {
    return $this->base->cacheFile($this->name);
  }

  function pathsCacheKey() {
    return 'sflmPaths'.$this->cacheSuffix();
  }

  /**
   * Добавляет путь, если библиотека существует в одном из зарегистрированных статических каталогов
   *
   * @param string Относительный путь к библиотеке
   * @param bool Вызывать ли ошибку, если библиотека не найдена
   * @throws Exception
   */
  function addStaticLib($lib, $strict = false) {
    foreach (Sflm::$absBasePaths as $k => $path) {
      if (file_exists("$path/{$this->base->type}/$lib")) {
        Sflm::output("add global lib $path/{$this->base->type}/$lib");
        $this->addLib("$k/{$this->base->type}/$lib");
        return;
      }
    }
    if ($strict) throw new Exception("Global lib '$lib' does not exists");
  }

  protected $changed = false;

  function addLib($lib, $strict = false) {
    if ($this->stored) throw new Exception("Can't add after frontend was stored. Reset or rerun frontend");
    if (!$strict and !$this->base->exists($lib)) {
      Sflm::output("Lib '$lib' does not exists");
      return $this;
    }
    Sflm::output("Adding lib '$lib'");
    foreach ($this->base->getPaths($lib) as $path) $this->__addPath($path, "lib '$lib'");
    return $this;
  }

  abstract protected function __addPath($path, $source = null);

  /**
   * @param string $path Добавляет к текущему фронтенду runtime путь
   * @throws Exception
   */
  function _addPath($path) {
    if ($this->base->isPackage($path)) throw new Exception("Can not add packages");
    if (in_array($path, $this->pathsCache)) {
      Sflm::output("New path '$path' already exists");
      return;
    }
    $this->newPaths[] = $path;
    $this->pathsCache[] = $path;
    $this->changed = true;
  }

  function getDeltaUrl() {
    if (!$this->newPaths) return false;
    return $this->base->getUrl($this->name.'new', $this->base->extractCode($this->newPaths), true);
  }

  protected function versionCacheKey() {
    return $this->base->type.ucfirst($this->name).'Version';
  }

  function version() {
    return ProjectState::get($this->versionCacheKey(), true) ? : 0;
  }

  protected $incremented = false;

  function incrementVersion() {
    if ($this->incremented) return false;
    $this->incremented = true;
    $version = $this->version() + 1;
    ProjectState::update($this->versionCacheKey(), $version);
    $this->base->version = $version;
    return true;
  }

}
