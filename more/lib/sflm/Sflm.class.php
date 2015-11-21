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
   * @throws Exception
   */
  static function frontend($type, $name = null) {
    if (!preg_match('/js|css/', $type)) throw new Exception("Unknown type '$type'");
    $name = $name ? : self::$frontendName;
    if (isset(self::$cache[$name][$type])) return self::$cache[$name][$type];
    Sflm::log("Generate frontend [$type::$name] instance");
    $class = 'SflmFrontend'.ucfirst($type);
    /* @var $frontend SflmFrontend */
    $frontend = new $class(self::lib($type), $name);
    if (!isset(self::$cache[$name])) self::$cache[$name] = [];
    return self::$cache[$name][$type] = $frontend;
  }

  // --

  static $debug = false, $output = false, $forceCache, $version = false, $strictMode, $cache = [];
  static protected $frontendName = null;

  static function clearCache() {
    Dir::clear(Sflm::$webPath.'/js');
    Dir::clear(Sflm::$webPath.'/css');
    SflmCache::clean();
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
   * @param $name
   * @param bool $quietly
   * @throws Exception
   */
  static function setFrontendName($name = 'default', $quietly = false) {
    if (self::$frontendName) {
      if ($quietly === false) {
        throw new Exception('Frontend name already set. Use reset. Backtrace of first set call: '."\n".CliColors::colored(self::$setFrontendBacktrace, 'darkGray'));
      }
      return;
    }
    self::$frontendName = $name;
    self::$setFrontendBacktrace = getBacktrace(false, 1);
  }

  static function getTags() {
    return Sflm::frontend('js')->getTags()."\n".Sflm::frontend('css')->getTags();
  }

  static function frontendName($strict = false) {
    if ($strict and !self::$frontendName) throw new Exception('Frontend name not defined');
    return self::$frontendName ? : false;
  }

  static function reset() {
    self::$frontendName = null;
    self::$cache = [];
  }

  /**
   * Создаёт Фронтенд, а если он уже существует удаляет его предварительно
   *
   * @param $type
   * @param null $name
   * @return SflmFrontend
   * @throws Exception
   */
  static function setFrontend($type, $name = null) {
    if ($name) self::$frontendName = $name;
    if (self::$frontendName) {
      if (!isset(self::$cache[self::$frontendName][$type])) {
        return self::frontend($type, $name);
      }
      Sflm::log("Delete Frontend [$type::".self::$frontendName."] instance");
      unset(self::$cache[self::$frontendName][$type]);
    }
    return self::frontend($type, $name);
  }

  static function stripCommentsExceptMeta($c) {
    return preg_replace('/\/\/(?! @).*/', '', preg_replace('!/\*.*?\*/!s', '', $c));
  }

  static function stripComments($c) {
    return preg_replace('/\/\/.*/', '', $c);
  }

  static function getCode($file) {
    return self::stripCommentsExceptMeta(file_get_contents($file));
  }

  /**
   * @param $absPath
   * @param null $whyDoUWantToGetThis
   * @return  string
   * @throws Exception
   */
  static function getPath($absPath, $whyDoUWantToGetThis = null) {
    foreach (Sflm::$absBasePaths as $folder => $absBasePath) {
      if (Misc::hasPrefix($absBasePath, $absPath)) {
        return $folder.Misc::removePrefix($absBasePath, $absPath);
      }
    }
    throw new Exception('"'.$absPath.'" not found'.($whyDoUWantToGetThis ? ". Getting for: $whyDoUWantToGetThis" : ''));
  }

  static function log($s) {
    if (self::$log) LogWriter::str('sflm', $s);
    if (self::$output) {
      if (strstr($s, 'Adding path')) outputColor($s, 'lightCyan');
      elseif (strstr($s, 'Skipped')) outputColor($s, 'darkGray');
      elseif (strstr($s, 'src: root')) outputColor($s, 'yellow');
      else strstr($s, 'src:') ? outputColor($s, 'cyan') : output($s);
    }
  }

  static $absBasePaths;
  static $baseFolders = [];
  static $log = true; // set true for debug
  static $debugPaths = [];
  static $debugUrl;
  static $webPath;
  static $namespace = 'Default';
  static $buildMode;

}

Sflm::$strictMode = IS_DEBUG;
Sflm::$debug = getConstant('DEBUG_STATIC_FILES');
Sflm::$forceCache = getConstant('FORCE_STATIC_FILES_CACHE');
Sflm::$absBasePaths = [
  'i' => NGN_PATH.'/i'
];
Sflm::$output = true; // set true to debug
Sflm::$webPath = defined('WEBROOT_PATH') ? WEBROOT_PATH.'/'.UPLOAD_DIR : NGN_ENV_PATH.'/temp';
Sflm::$buildMode = getConstant('BUILD_MODE');
