<?php

/**
 * Коллекцианирует и кэширует пути к статическим файлам во время выполнения приложения
 */
class SflmFrontend {

  public $frontend, $sflm, $paths;

  function __construct(SflmBase $sflm, $frontend = null) {
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

  /*
  protected function storePaths(array $paths) {
    $this->sflm->storeLib($this->frontend, $this->sflm->extractCode($paths));
    return $this;
  }
  */

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
    $changed = false;
    if ($lib == 'pageBlocks') die2($newPaths);
    foreach ($newPaths as $path) {
      if (in_array($path, $this->getPathsCache())) {
        output("New path '$path' already exists");
        continue;
      }
      $this->addPath($path);
      $changed = true;
    }
    if ($changed) {
      NgnCache::c()->save($this->paths, $this->pathsCacheKey());
      output("update stored file after adding lib '$lib'");
      $this->store();
      $this->incrementVersion();
    }
  }

  protected function addPath($path) {
    $this->paths[] = $path;
  }

  protected function versionCacheKey() {
    return $this->sflm->type.ucfirst($this->frontend).'Version';
  }

  function version() {
    return Config::getVar($this->versionCacheKey(), true) ? : 0;
  }

  function incrementVersion() {
    LogWriter::v('incrementVersion', 1);
    SiteConfig::updateVar($this->versionCacheKey(), (Config::getVar($this->versionCacheKey(), true) ? : 0) + 1);
  }

}
