<?php

require_once NGN_PATH.'/vendors/Zend/Cache.php';
require_once NGN_PATH.'/vendors/Zend/Cache/Core.php';
require_once NGN_PATH.'/vendors/Zend/Cache/Backend.php';
require_once NGN_PATH.'/vendors/Zend/Cache/Frontend/Function.php';
require_once NGN_PATH.'/vendors/Zend/Cache/Backend/Interface.php';
require_once NGN_PATH.'/vendors/Zend/Cache/Backend/ExtendedInterface.php';
require_once NGN_PATH.'/vendors/Zend/Cache/Backend/File.php';

class FileCache {

  protected $options = [];

  function __construct(array $options) {
    if (!defined('DATA_PATH')) throw new Exception('DATA_PATH not defined');
    $this->options = $options;
    if (!file_exists(static::folder($options))) Dir::make(static::folder());
  }

  // --

  static protected $cache = [];

  static function c(array $options = []) {
    $key = serialize($options);
    if (isset(static::$cache[$key])) return static::$cache[$key];
    $options['automatic_serialization'] = true;
    return static::$cache[$key] = Zend_Cache::factory('Core', 'File', $options, array_merge($options, [
      'cache_dir' => static::folder($options)
    ]));
  }

  static function folder() {
    return DATA_PATH.'/cache';
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

  /**
   * Удаляет все данные кэша по этому тэгу
   *
   * @param   string  Zend_Cache-тэг
   */
  static function cleanTag($tag) {
    self::c()->clean(Zend_Cache::CLEANING_MODE_ALL, [$tag]);
  }

  /**
   * Удаляет весь кэш
   *
   * @param   string  Zend_Cache-тэг
   */
  static function clean() {
    Dir::clear(self::folder());
  }

}
