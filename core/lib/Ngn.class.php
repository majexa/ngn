<?php

class Ngn {

  static protected $events = [];

  static function addEvent($name, Closure $func) {
    self::$events[$name] = $func;
  }

  static function fireEvent($name) {
    LogWriter::str('events', "fire event '$name'");
    $params = array_slice(func_get_args(), 1);
    if (isset(self::$events[$name])) {
      return call_user_func_array(self::$events[$name], $params);
    }
    elseif (($func = Config::getVar('event/'.$name, true)) !== false and is_callable($func)) {
      return call_user_func_array($func, $params);
    }
  }

  static $_basePaths = [];
  static $basePaths = [];

  /**
   * @param $path
   * @param int $priority Чем больше число, тем выше приоритет.
   *                      Используйте приоритет от 1 до 4 для подключения библиотек.
   *                      Для папки проекта "site" в Ngn используется приоритет 5.
   */
  static function addBasePath($path, $priority = 0, $sflmPathPackage = null, $sflmPackageSubfolder = 'm') {
    if (file_exists("$path/lib")) Lib::addFolder("$path/lib");
    require_once __DIR__.'/file/Config.class.php';
    Config::addBasePath("$path/config", $priority);
    self::$_basePaths[] = [$path, $priority];
    self::$_basePaths = Arr::sortByOrderKey(self::$_basePaths, 1, SORT_DESC);
    self::$basePaths = Arr::get(self::$_basePaths, 0);
    if ($sflmPathPackage !== null) Sflm::$absBasePaths[$sflmPathPackage] = $path.'/'.$sflmPackageSubfolder;
  }

  static function debugKey() {
    return file_exists(NGN_PATH.'/.debugKey') ? file_get_contents(NGN_PATH.'/.debugKey') : false;
  }

}

Ngn::addBasePath(NGN_PATH.'/core');