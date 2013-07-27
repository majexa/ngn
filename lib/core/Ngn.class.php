<?php

class Ngn {

  static protected $events = [];

  static function addEvent($name, Closure $func) {
    self::$events[$name] = $func;
  }
  
  static function fireEvent($name, $params = null) {
    LogWriter::str('events', "fire event '$name''");
    $params = (array)$params;
    if (($func = Config::getVar('event/'.$name, true)) !== false and is_callable($func))
      return call_user_func_array($func, $params);
    elseif (isset(self::$events[$name]))
      return call_user_func_array(self::$events[$name], $params);
  }

  static protected $_basePaths = [];
  static $basePaths = [];

  static function addBasePath($path, $priority = 0) {
    if (file_exists("$path/lib")) Lib::addFolder("$path/lib");
    require_once __DIR__.'/Config.class.php';
    Config::addBasePath("$path/config", $priority);
    self::$_basePaths[] = [$path, $priority];
    self::$basePaths = Arr::get(Arr::sortByOrderKey(self::$_basePaths, 1, SORT_DESC), 0);
  }

}

Ngn::addBasePath(NGN_PATH);