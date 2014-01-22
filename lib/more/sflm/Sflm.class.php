<?php

class Sflm {

  static $debug = false;

  static $forceCache;

  static $version = false;

  static $absBasePaths;

  static function clearCache() {
    Dir::clear(UPLOAD_PATH.'/js');
    Dir::clear(UPLOAD_PATH.'/css');
    NgnCache::clean();
    O::delete('SflmJs');
    O::delete('SflmCss');
    self::$cache = [];
  }

  /**
   * @var Ключ для кэширования библиотек. Должен определяться в роутере
   */
  static $frontend;

  static function setFrontend($frontend) {
    if (!isset(self::$frontend)) self::$frontend = $frontend;
  }

  static function resetFrontend($frontend) {
    if (isset(self::$frontend)) unset(self::$cache[self::$frontend]);
    self::$frontend = $frontend;
  }

  static function getTags() {
    return Sflm::flm('js')->getTags()."\n".Sflm::flm('css')->getTags();
  }

  static $cache = [];

  /**
   * Library Manager
   *
   * @param js/css
   * @return SflmBase
   */
  static function lm($type) {
    return O::get('Sflm'.ucfirst($type));
  }

  static function jsClass() {
    static $o;
    if (isset($o)) return $o;
    return $o = new SflmJsClass(self::flm('js'));
  }

  /**
   * Frontend Library Manager
   *
   * @param js/css
   * @return SflmFrontend
   */
  static function flm($type, $frontend = null) {
    $frontend = $frontend ? : self::$frontend;
    if (isset(self::$cache[$frontend][$type])) return self::$cache[$frontend][$type];
    $class = 'SflmFrontend'.ucfirst($type);
    /* @var $sflmFrontend SflmFrontend */
    $sflmFrontend = new $class(self::lm($type), $frontend);
    //$sflmFrontend->store();
    if (!isset(self::$cache[$frontend])) self::$cache[$frontend] = [];
    return self::$cache[$frontend][$type] = $sflmFrontend;
  }

  static function reset($type, $frontend = null) {
    $frontend = $frontend ? : self::$frontend;
    unset(self::$cache[$frontend][$type]);
    return self::flm($type, $frontend);
  }

  static function output($s) {
    if (IS_DEBUG) output($s);
  }

}

Sflm::$debug = getConstant('DEBUG_STATIC_FILES');
Sflm::$forceCache = getConstant('FORCE_STATIC_FILES_CACHE');
Sflm::$absBasePaths = [
  //'u' => WEBROOT_PATH.'/'.UPLOAD_DIR,
  'm' => WEBROOT_PATH.'/m',
  'i' => NGN_PATH.'/i'
];
