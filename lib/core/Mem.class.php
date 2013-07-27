<?php

class Mem {

  static $keyPrefix = '';
  static $initialized = false;
  static $m;
  static $enable = false;

  /**
   * @return Memcached
   */
  static function getMemcache() {
    if (!static::$initialized) {
      static::init();
      static::$initialized = true;
    }
    if (isset(self::$m)) return self::$m;
    self::$m = new Memcached;
    if (!self::$m->addServer('localhost', 11211)) throw new Exception("Could not connect to memcached");
    return self::$m;
  }

  static protected function init() {
  }

  static function get($key) {
    if (!self::$enable) return false;
    return self::getMemcache()->get(static::$keyPrefix.$key);
  }

  static function set($key, $val, $expires = 0) {
    if (!self::$enable) return;
    self::getMemcache()->set(static::$keyPrefix.$key, $val, $expires);
  }

  static function delete($key) {
    if (!self::$enable) return;
    self::getMemcache()->delete(static::$keyPrefix.$key);
  }

  static function setIfNotExists($key, $val, $expires = 60) {
    if (self::get($key) !== false) throw new Exception("$key already exists.");
    self::set($key, $val, $expires);
  }

  static function getAndDelete($key) {
    $v = self::get($key);
    self::delete($key);
    return $v;
  }

  static function clean() {
    if (!self::$enable) return;
    self::getMemcache()->flush();
  }

}

Mem::$enable = class_exists('Memcached');