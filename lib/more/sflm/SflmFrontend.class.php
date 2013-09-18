<?php

/**
 * Коллекцианирует и кэширует пути к статическим файлам во время выполнения приложения
 */
class SflmFrontend {

  public $frontend, $sflm, $paths, $newPaths = [], $id, $debug = false;

  function __construct(SflmBase $sflm, $frontend = null) {
    $this->id = Misc::randString(5);
    $this->sflm = $sflm;
    $this->frontend = $frontend ? : Sflm::$frontend;
    Misc::checkEmpty($this->frontend, '$this->frontend');
    $this->sflm->version = $this->version();
    $this->paths = $this->getPathsCache();
    if ($this->getLastPaths() != $this->getPaths()) {
      $this->incrementVersion();
      $this->storeLastPaths();
    }
    $this->init();
  }

  function reload() {

  }

  protected function getLastPaths() {
    return NgnCache::c()->load('sflmLastPaths'.$this->sflm->type.$this->frontend) ? : [];
  }

  protected function storeLastPaths() {
    NgnCache::c()->save($this->getPaths(), 'sflmLastPaths'.$this->sflm->type.$this->frontend);
  }

  function getPathsCache() {
    return NgnCache::c()->load($this->pathsCacheKey()) ? : [];
  }

  /**
   * Возвращает статические (SflmBase) и динамические (SflmFrontend) пути вместе
   */
  function getPaths() {
    return array_merge($this->sflm->getPaths($this->frontend), $this->paths);
  }

  protected function init() {
  }

  public $extraCode = "\n//***";

  function code() {
    return $this->sflm->extractCode($this->getPaths());
  }

  function getTags() {
    return $this->sflm->getTags($this->frontend, $this->code());
  }

  function getTag() {
    return $this->sflm->getTag($this->frontend);
  }

  /**
   * Сохраняет в конечный файл статические и динамические пути
   */
  function store() {
    if ($this->sflm->storeLib($this->frontend, $this->code())) $this->incrementVersion();
  }

  function filePath() {
    return $this->sflm->filePath($this->frontend);
  }

  function cacheFile() {
    return $this->sflm->cacheFile($this->frontend);
  }

  function pathsCacheKey() {
    return 'sflmPaths'.$this->sflm->type.$this->frontend;
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
      if (file_exists("$path/{$this->sflm->type}/$lib")) {
        output("add global lib $path/{$this->sflm->type}/$lib");
        $this->addLib("$k/{$this->sflm->type}/$lib");
        return;
      }
    }
    if ($strict) throw new Exception("Global lib '$lib' does not exists");
  }

  protected $changed = false;

  /**
   * Добавляет в runtime-кэш библиотеку
   *
   * @param package
   * @param path / package
   */
  function addLib($lib, $strict = false) {
    if (!$strict and !$this->sflm->exists($lib)) {
      output("Lib '$lib' already exists");
      return;
    }
    output("Adding lib '$lib'");
    $newPaths = $this->sflm->getPaths($lib);
    foreach ($newPaths as $path) {
      if (in_array($path, $this->getPathsCache())) {
        output("New path '$path' already exists");
        continue;
      }
      $this->addPath($path);
    }
    if ($this->changed) {
      NgnCache::c()->save($this->paths, $this->pathsCacheKey());
      output("update stored file after adding lib '$lib'");
      $this->store();
      $this->incrementVersion();
    }
  }

  function getDeltaUrl() {
    if (!$this->newPaths) return false;
    return $this->sflm->getUrl($this->frontend.'new', $this->sflm->extractCode($this->newPaths), true);
  }

  protected function addPath($path) {
    $this->newPaths[] = $path;
    $this->paths[] = $path;
    $this->changed = true;
  }

  protected function versionCacheKey() {
    return $this->sflm->type.ucfirst($this->frontend).'Version';
  }

  function version() {
    return Config::getVar($this->versionCacheKey(), true) ? : 0;
  }

  function incrementVersion() {
    $version = (Config::getVar($this->versionCacheKey(), true) ? : 0) + 1;
    SiteConfig::updateVar($this->versionCacheKey(), $version);
    $this->sflm->version = $version;
  }

}
