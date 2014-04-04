<?php

/**
 * Memcached critical
 */
class Memc {

  static $enable = false;

  static function get($k) {
    if (!self::$enable) return false;
    if (($v = ProjMem::get($k)) !== false) return $v;
    // Если ключа нет, ищем его в БД. Но нужно учесть, что его позже нужно обязательно
    // сохранить в memcached
    if (($v = db()->selectCell('SELECT v FROM memcache WHERE k=?', $k)) === false)
      return false;
    return unserialize($v);
  }
  
  static function set($k, $v) {
    if (!self::$enable) return;
    ProjMem::set($k, $v);
    db()->query('REPLACE INTO memcache SET k=?, v=?', $k, serialize($v));
  }
  
  static function delete($k) {
    if (!self::$enable) return;
    ProjMem::delete($k);
    db()->query('DELETE FROM memcache WHERE k=?', $k);
  }
  
  static function clean() {
    if (!self::$enable) return;
    ProjMem::clean();
    db()->query('TRUNCATE TABLE memcache');
  }
  
}