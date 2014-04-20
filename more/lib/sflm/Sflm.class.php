<?php

class Sflm {

  /**
   * Library Level
   *
   * @param js /css
   * @return SflmBase
   */
  static function lib($type) {
    return O::get('Sflm'.ucfirst($type));
  }

  /**
   * Frontend Level
   *
   * @param $type
   * @param null $name
   * @return SflmFrontend
   */
  static function frontend($type, $name = null) {
    if (!preg_match('/js|css/', $type)) throw new Exception("Unknown type '$type'");
    $name = $name ? : self::$frontendName;
    if (isset(self::$cache[$name][$type])) return self::$cache[$name][$type];
    Sflm::output("Generate frontend [$type::$name] instance");
    $class = 'SflmFrontend'.ucfirst($type);
    /* @var $frontend SflmFrontend */
    $frontend = new $class(self::lib($type), $name);
    if (!isset(self::$cache[$name])) self::$cache[$name] = [];
    return self::$cache[$name][$type] = $frontend;
  }

  // --

  static $debug = false, $output = false, $forceCache, $version = false, $absBasePaths, $strictMode, $cache = [];
  static protected $frontendName = null;

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
  static function setFrontendName($frontend) {
    if (self::$frontendName) {
      throw new Exception('Frontend name already set. Use reset. Backtrace of first set call: '."\n".O::get('CliColors')->getColoredString(self::$setFrontendBacktrace, 'darkGray'));
    }
    self::$frontendName = $frontend;
    self::$setFrontendBacktrace = getBacktrace(false, 1);
  }

  static function getTags() {
    return Sflm::frontend('js')->getTags()."\n".Sflm::frontend('css')->getTags();
  }

  static function frontendName() {
    return self::$frontendName ? : false;
  }

  static function reset() {
    self::$frontendName = null;
    self::$cache = [];
  }

  static function setFrontend($type, $name = null) {
    if ($name) self::$frontendName = $name;
    if (self::$frontendName) {
      if (!isset(self::$cache[self::$frontendName][$type])) {
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
Sflm::$output = false;
