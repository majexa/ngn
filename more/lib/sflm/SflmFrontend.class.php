<?php

/**
 * Коллекцианирует и кэширует пути к статическим файлам во время выполнения приложения
 */
abstract class SflmFrontend {

  /**
   * @var string Имя фронтенда
   */
  public $name;

  public $pathsCache, $id, $debug = false;

  public $base, $newPaths = [];

  function __construct(SflmBase $base, $name) {
    $this->id = Misc::randString(5);
    $this->base = $base;
    $this->name = $name;
    Misc::checkEmpty($this->name, 'Frontend name not defined');
    $this->base->version = $this->version();
    $this->pathsCache = $this->getPathsCache() ?: $this->getStaticPaths();
    $this->init();
  }

  protected function getStaticPaths() {
    return $this->base->getPaths($this->name, true);
  }

  /**
   * Возвращает статические (SflmBase) и динамические (SflmFrontend) пути вместе
   */
  function getPaths() {
    return $this->pathsCache;
  }

  protected function cacheSuffix() {
    return $this->base->type.$this->key();
  }

  function key() {
    return str_replace('/', '_', $this->name);
  }

  protected function getLastPaths() {
    return SflmCache::c()->load('sflmLastPaths'.$this->cacheSuffix()) ?: [];
  }

  protected function storeLastPaths() {
    SflmCache::c()->save($this->getPaths(), 'sflmLastPaths'.$this->cacheSuffix());
  }

  /**
   * Возвращает сохраненные для текущего фронтенда runtime пути
   *
   * @return array
   */
  function getPathsCache() {
    return SflmCache::c()->load($this->pathsCacheKey()) ?: [];
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
    $this->checkStored();
    $html = $this->base->getTags($this->name, $this->_code());
//    if (isset(Sflm::$debugPaths[$this->base->type])) {
//      foreach (Sflm::$debugPaths[$this->base->type] as $path) {
//        $html .= $this->base->getTag((isset(Sflm::$debugUrl) ? Sflm::$debugUrl : '').'/'.ltrim($path, '/'));
//      }
//    }
    foreach ($this->debugPaths as $path) {
      $html .= $this->base->getTag((isset(Sflm::$debugUrl) ? Sflm::$debugUrl : '').'/'.ltrim($path, '/'));
    }
    return $html;
  }

  function getTag() {
    return $this->base->getTag($this->name);
  }

  protected $stored = false;

  /**
   * Сохраняет все новые пути фронтенда в кэш. После выполнения этого метода в фронтенд уже нельзя добавлять ничего
   *
   * @param string $source
   * @return $this
   * @throws Exception
   */
  function store($source = 'direct') {
    $this->checkNotStored();
    $this->storeBacktrace = getBacktrace(false);
    if (!$this->newPaths) {
      $this->stored = true;
      Sflm::output("No new paths. Storing skipped");
      return;
    }
    Sflm::output("Update collected '{$this->name}.{$this->base->type}' file after adding lib ".($source ? "from '$source' source" : ''));
    $this->storePaths();
    if ($this->base->storeLib($this->name, $this->code())) {
      $this->incrementVersion();
      $this->stored = true;
    }
  }

  protected $storeBacktrace;

  function storePaths() {
    SflmCache::c()->save($this->getPaths(), $this->pathsCacheKey());
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
   * @param string $lib Относительный путь к библиотеке
   * @param bool $strict Вызывать ли ошибку, если библиотека не найдена
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

  protected function checkStored() {
    if (!$this->stored) throw new Exception('Frontend must be stored [SflmFrontend::store()] before getting tags. Inject your sflm-tags by {sflm} string');
  }

  protected function checkNotStored() {
    if ($this->stored) throw new Exception("Can't store after frontend was already stored. Reset or rerun frontend. Backtrace of first call:\n".$this->storeBacktrace);
  }

  function addLib($lib, $strict = false) {
    $this->checkNotStored();
    if (!$strict and !$this->base->exists($lib)) {
      Sflm::output("Lib '$lib' does not exists");
      return $this;
    }
    Sflm::output("Adding lib '$lib'");
    foreach ($this->base->getPaths($lib) as $path) $this->__addPath($path, "lib '$lib'");
    return $this;
  }

  abstract protected function __addPath($path, $source = null);

  protected $debugPaths = [];

  /**
   * Добавляет к текущему фронтенду runtime путь
   *
   * @param string $path
   * @throws Exception
   */
  function _addPath($path) {
    if ($this->base->isPackage($path)) throw new Exception("Can not add packages");
    if (in_array($path, $this->pathsCache)) {
      Sflm::output("New path '$path' already exists");
      return;
    }
    if (isset(Sflm::$debugPaths[$this->base->type]) and Arr::strExistsInvert(Sflm::$debugPaths[$this->base->type], $path)) {
      $this->debugPaths[] = $path;
      Sflm::output('Skipping debug path '.$path);
      return;
    }
    Sflm::output('Adding path '.$path);
    $this->newPaths[] = $path;
    $this->pathsCache[] = $path;
  }

  function getDeltaUrl() {
    if (!$this->newPaths) return false;
    $this->checkStored();
    return $this->base->getUrl($this->name.'new', $this->base->extractCode($this->newPaths), true);
  }

  protected function versionCacheKey() {
    return $this->base->type.ucfirst($this->name).'Version';
  }

  function version() {
    return ProjectState::get($this->versionCacheKey(), true) ?: 0;
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
