<?php

require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/Function.php';

if (!defined('CACHE_METHOD')) define('CACHE_METHOD', 'File');

class NgnCache {

  /**
   * Удаляет все данные кэша по этому тэгу
   *
   * @param   string  Zend_Cache-тэг
   */
  static function cleanTag($tag) {
    self::c()->clean(Zend_Cache::CLEANING_MODE_ALL, [$tag]);
  }

  static function clean() {
    self::c()->clean(Zend_Cache::CLEANING_MODE_ALL);
    if (CACHE_METHOD == 'File') self::fc()->clean(Zend_Cache::CLEANING_MODE_ALL);
  }

  static protected $cache;

  /**
   * Usage:
   * self::c()->load('key');
   * self::c()->save($data, 'key', ['tag_1', 'tag_2']);
   *
   * @return Zend_Cache_Core
   */
  static function c(array $frontendOptions = []) {
    if (defined('DATA_CACHE') and DATA_CACHE === false) $frontendOptions['caching'] = false;
    $frontendOptions['automatic_serialization'] = true;
    if (!defined('PROJECT_KEY')) throw new Exception('PROJECT_KEY not defined');
    //$cacheId = empty($frontendOptions) ? '' : md5(serialize($frontendOptions));
    if (empty($frontendOptions) and isset(self::$cache)) return self::$cache;
    $frontendOptions['cache_id_prefix'] = PROJECT_KEY;
    if (defined('CACHE_METHOD') and CACHE_METHOD == 'Memcached') {
      require_once 'Zend/Cache/Backend/Memcached.php';
      self::$cache = Zend_Cache::factory('Core', 'Memcached', $frontendOptions, [
          'servers' => [
            [
              'host'             => 'localhost',
              'port'             => 11211,
              'persistent'       => true,
              'weight'           => 1,
              'timeout'          => 5,
              'retry_interval'   => 15,
              'status'           => true,
              'failure_callback' => 'error'
            ]
          ]
        ]);
    }
    else {
      self::$cache = self::_fc($frontendOptions);
    }
    return self::$cache;
  }

  static protected $fileCache, $folder = '/cache';

  /**
   * File Cache
   */
  static function fc(array $frontendOptions = []) {
    if (empty($frontendOptions) and isset(self::$fileCache)) return self::$fileCache;
    self::$fileCache = self::_fc($frontendOptions);
    return self::$fileCache;
  }

  static protected function _fc(array $frontendOptions = []) {
    require_once 'Zend/Cache/Backend/File.php';
    if (!defined('DATA_PATH')) throw new Exception('DATA_PATH not defined');
    $frontendOptions['automatic_serialization'] = true;
    if (!file_exists(DATA_PATH.static::$folder)) Dir::make(DATA_PATH.static::$folder);
    return Zend_Cache::factory('Core', 'File', $frontendOptions, ['cache_dir' => DATA_PATH.static::$folder]);
  }

  static function func($func, $id, $force = false) {
    $cache = self::c();
    if (!$force and ($r = $cache->load($id)) !== false) {
      return $r;
    }
    $r = $func();
    $cache->save($r, $id);
    return $r;
  }

}

class NgnCacheDdi extends NgnCache {
  static $folder = '/cacheddi';
}


