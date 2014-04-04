<?php

class Sflm {

  static $debug = false, $output = false, $forceCache, $version = false, $absBasePaths, $strictMode, $cache = [];
  static protected $frontendName;

  static function clearCache() {
    Dir::clear(UPLOAD_PATH.'/js');
    Dir::clear(UPLOAD_PATH.'/css');
    FileCache::clean();
    O::delete('SflmJs');
    O::delete('SflmCss');
    self::$cache = [];
  }

  protected static $setFrontendBacktrace;

  /**
   * Определяет ключ для кэширования библиотек
   * Должен определяться в роутере до начала sflm-рантайма
   * В рамках одного рантайма может быть определен только один фронтенд
   * Для сброса рантайма используйте resetFrontend
   *
   * @param $frontend
   * @throws Exception
   */
  static function setFrontend($frontend) {
    if (isset(self::$frontendName)) {
      throw new Exception('Frontend already set in . Use reset. Backtrace of first set call: '."\n".self::$setFrontendBacktrace);
    }
    self::$frontendName = $frontend;
    self::$setFrontendBacktrace = getBacktrace(false);
  }

  static function getTags() {
    return Sflm::frontend('js')->getTags()."\n".Sflm::frontend('css')->getTags();
  }

  /**
   * Library Level
   *
   * @param js/css
   * @return SflmBase
   */
  static function lib($type) {
    return O::get('Sflm'.ucfirst($type));
  }

  static function frontendName() {
    return isset(self::$frontendName) ? self::$frontendName : false;
  }

  /**
   * Frontend Level
   *
   * @param $type
   * @param null $name
   * @return SflmFrontend
   */
  static function frontend($type, $name = null) {
    $name = $name ? : self::$frontendName;
    if (isset(self::$cache[$name][$type])) return self::$cache[$name][$type];
    Sflm::output("Generate frontend [$type::$name] instance");
    $class = 'SflmFrontend'.ucfirst($type);
    /* @var $sflmFrontend SflmFrontend */
    $sflmFrontend = new $class(self::lib($type), $name);
    if (!isset(self::$cache[$name])) self::$cache[$name] = [];
    return self::$cache[$name][$type] = $sflmFrontend;
  }

  static function resetFrontend($type, $name = null) {
    if ($name) self::$frontendName = $name;
    if (isset(self::$frontendName)) {
      if (!isset(self::$cache[self::$frontendName][$type])) {
        Sflm::output("Try to reset. Frontend [$type::".self::$frontendName."] instance does not exists. Skipped");
        return self::frontend($type, $name);
      }
      Sflm::output("Delete Frontend [$type::".self::$frontendName."] instance");
      unset(self::$cache[self::$frontendName][$type]);
    }
    return self::frontend($type, $name);
  }

  static function output($s) {
    if (self::$output) {
      if (strstr($s, 'src: direct')) output3($s);
      else strstr($s, 'src:') ? output2($s) : output($s);
    }
  }

}

Sflm::$strictMode = IS_DEBUG;
Sflm::$debug = getConstant('DEBUG_STATIC_FILES');
Sflm::$forceCache = getConstant('FORCE_STATIC_FILES_CACHE');
Sflm::$absBasePaths = [
  'i' => NGN_PATH.'/i'
];
Sflm::$output = true;
