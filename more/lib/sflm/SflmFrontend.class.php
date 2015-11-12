<?php

/**
 * Коллекцианирует и кэширует пути к статическим файлам во время выполнения приложения
 */
abstract class SflmFrontend {

  /**
   * @var string Имя фронтенда
   */
  public $name;

  /**
   * Пути ко всем ресурсам фроентенда, сохранённые в кэше. Тут собраны как статические пути, так и runtime-пути.
   *
   * @var
   */
  public $pathsCache, $absPathsCache;

  public $id, $debug = false;

  public $base;

  public $newPaths = [], $newAbsPaths = [];

  function __construct(SflmBase $base, $name) {
    $this->id = Misc::randString(5);
    $this->base = $base;
    $this->name = $name;
    Misc::checkEmpty($this->name, 'Frontend name not defined');
    $this->base->version = $this->version();
    $this->pathsCache = $this->getPathsCache() ?: $this->getStaticPaths();
    $this->absPathsCache = $this->getAbsPathsCache();
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

  /**
   * @api
   * Возвращает код Sflm-фронтенда
   *
   * @return string
   * @throws Exception
   */
  function code() {
    if ($this->stored) throw new Exception("Can't get code after frontend was stored. Reset frontend");
    return $this->_code();
  }

  protected $absPaths = [];

  /**
   * @api
   * Добавляет все файлы в каталоге к Sflm-фронтенду
   *
   * @param $absFolder
   */
  function addFolder($absFolder) {
    foreach (glob($absFolder.'/*.'.$this->base->type) as $file) {
      $this->addFile($file);
    }
  }

  /**
   * @api
   * Добавляет файл к Sflm-фронтенду
   *
   * @param $file
   */
  function addFile($file) {
    if (in_array($file, $this->absPathsCache)) return;
    $this->absPathsCache[] = $file;
      $this->newAbsPaths[] = $file;
  }

  /**
   * Возвращает сохраненные для текущего фронтенда runtime пути
   *
   * @return array
   */
  function getAbsPathsCache() {
    return SflmCache::c()->load($this->absPathsCacheKey()) ?: [];
  }

  function _code() {
//    $k = md5(serialize($this->getPaths()));
//    if (($r = Mem::get($k)) !== false) {
//      return $r;
//    }
    $code = $this->base->extractCode($this->getPaths());
    foreach ($this->absPathsCache as $file) $code .= "\n/*--|$file|--*/\n".file_get_contents($file);
//    Mem::set($k, $r);
    return $code;
  }

  /**
   * Возвращает HTML-тег ссылающийся на скомпилированый файл с указанием версии для clientSide-кэширования
   *
   * @return string
   * @throws Exception
   */
  function getTags() {
    $html = $this->base->getTags($this->name, $this->_code());
    $html .= $this->addDebugTags();
    return $html;
  }

  protected function addDebugTags() {
    return '';
  }

  function getTag() {
    return $this->base->getTag($this->name);
  }

  protected $stored = false;

  /**
   * @api
   * Сохраняет все новые пути кэш данных и создаёт веб-кэш. После выполнения этого метода в фронтенд уже нельзя добавлять ничего
   *
   * @param string $source
   * @return $this
   * @throws Exception
   */
  function store($source = 'root') {
    $this->checkNotStored();
    $this->storeBacktrace = getBacktrace(false);
//    if (!$this->newPaths and !$this->newAbsPaths) {
//      $this->stored = true;
//      $this->log("No new paths. Storing skipped");
//      return;
//    }
    $this->log("Update collected '{$this->name}.{$this->base->type}' file after adding lib ".($source ? "from '$source' source" : ''));
    $this->storePaths();
    $this->storeAbsPaths();
    if (($file = $this->base->storeLib($this->name, $this->code())) !== false) {
      $this->uglify($file);
      $this->incrementVersion();
      $this->stored = true;
    }
  }

  abstract protected function uglify($file);

  /**
   * Этот метот нужно вызывать при создании проекта
   * @throws Exception
   */
  function initStore() {
    $this->base->storeLib($this->name, $this->code());
    $this->incrementVersion();
    $this->stored = true;
  }

  protected $storeBacktrace;

  protected function storePaths() {
    SflmCache::c()->save($this->getPaths(), $this->pathsCacheKey());
  }

  protected function storeAbsPaths() {
    SflmCache::c()->save($this->absPathsCache, $this->absPathsCacheKey());
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

  function absPathsCacheKey() {
    return 'sflmAbsPaths'.$this->cacheSuffix();
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
        $this->log("add global lib $path/{$this->base->type}/$lib");
        $this->addLib("$k/{$this->base->type}/$lib");
        return;
      }
    }
    if ($strict) throw new Exception("Global lib '$lib' does not exists");
    else $this->log("Global lib '$lib' does not exists");
  }

  protected function log($s) {
    Sflm::log($this->base->type.': '.$s);
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
      $this->log("Lib or path '$lib' does not exists");
      return $this;
    }
    $this->log("Adding lib '$lib'");
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
      $this->log("New path '$path' already exists");
      return;
    }
    if ($this->addDebugPath($path)) return;
    $this->log('Adding path '.$path);
//    prr('Adding path '.$path);
    $this->newPaths[] = $path;
    $this->pathsCache[] = $path;
  }

  /**
   * Добавляет путь к отладочным-путям sflm-фронтенда, если он является таковым
   *
   * @param $path
   * @return bool
   */
  function addDebugPath($path) {
    if (in_array($path, $this->debugPaths)) return true;
    if (isset(Sflm::$debugPaths[$this->base->type]) and Arr::strExistsInvert(Sflm::$debugPaths[$this->base->type], $path)) {
      $this->debugPaths[] = $path;
      $this->log('Adding debug path '.$path);
      return true;
    }
    return false;
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

  /**
   * Парсит собранный веб-кэш файл и возвращает пути файлов находящихся в нём
   *
   * @return array
   */
  function parseWebCachePaths() {
    $webPackageFile = Sflm::$webPath.'/'.$this->base->type.'/cache/'.$this->name.'.'.$this->base->type;
    preg_match_all('/\\/\\*--\\|(.*)\\|--\\*\\//', file_get_contents($webPackageFile), $m);
    return $m[1];
  }

}
