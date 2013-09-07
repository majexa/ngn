<?php

class Sflm {

  static $debug = false;

  static $forceCache = false;

  static $version = false;

  static $absBasePaths;

  static function clearCache() {
    Dir::clear(UPLOAD_PATH.'/js');
    Dir::clear(UPLOAD_PATH.'/css');
    NgnCache::clean();
  }

  /**
   * @var Ключ для кэширования библиотек. Должен определяться в роутере
   */
  static $frontend;

  static function setFrontend($frontend) {
    if (!isset(self::$frontend)) self::$frontend = $frontend;
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

  /**
   * Frontend Library Manager
   *
   * @param js/css
   * @return SflmFrontend
   */
  static function flm($type, $frontend = null) {
    if (isset(self::$cache[$type.$frontend])) return self::$cache[$type.$frontend];
    $frontend = $frontend ? : self::$frontend;
    $class1 = 'Sflm'.ucfirst($type);
    $class2 = 'SflmFrontend'.ucfirst($type);
    /* @var $sflmFrontend SflmFrontend */
    $sflmFrontend = new $class2(new $class1($frontend), $frontend);
    // ... $sflmFrontend->store(); ...
    //$sflmFrontend->store();
    //$sflmFrontend->pathsCacheKey();
    return self::$cache[$type.$frontend] = $sflmFrontend;
  }

  static function reset($type, $frontend = null) {
    unset(self::$cache[$type.$frontend]);
    return self::flm($type, $frontend);
  }

}

Sflm::$debug = getConstant('DEBUG_STATIC_FILES');
Sflm::$forceCache = getConstant('FORCE_STATIC_FILES_CACHE');

Sflm::$absBasePaths = [
  //'u' => WEBROOT_PATH.'/'.UPLOAD_DIR,
  //'m' => WEBROOT_PATH.'/m',
  'i' => NGN_PATH.'/i'
];