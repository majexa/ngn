<?php

function pearAutoload($class) {
  require_once str_replace('_', '/', $class).'.php';
}

/**
 * Класс отвечает за получение путей к файлам
 *
 * Тут нельзя и сользовать эксепшены. Их обработка появляется позже
 *
 */
class Lib {

  static $isCache = false;

  static $cl = [];

  /**
   * Поключает файл по пути относительно корня NGN-каталога, либо по имени класса
   *
   * @param   string  Путь к файлу относительно корня NGN-каталога или имя класса
   */
  static function required($_path, Req $req = null, $ext = '.php') {
    if (!in_array($_path, self::$cl)) self::$cl[] = $_path;
    if (self::vendorAutoloader($_path) !== false) return false;
    if (($path = self::getPath($_path, false, $ext)) == false) return false;
    if ($req) $_REQUEST = $req->r;
    require_once $path;
    return true;
  }

  static $paths;

  /**
   * Получает путь к файлу.
   * $path может быть:
   * - ClassName
   * - classLib/ClassName
   * - ngn/file.php
   * - ngn/lib/file.php
   * - site/lib/file.php
   *
   * @param   string  Возвращает абсолютный путь к файлу
   */
  static function getPath($path, $strict = true, $ext = '.php') {
    if (isset(self::$paths) and isset(self::$paths[$path])) return self::$paths[$path];
    if (!strstr($path, '/')) {
      // Если в Пути нет слэша, считаем, что это имя класса
      $r = self::getClassPath($path);
      if (!$r) {
        if ($strict) {
          Err::error("PathByClass '$path' not found");
        }
        else
          return false;
      }
      else {
        $existsPath = $r;
      }
    }
    else {
      $r = self::getFilePath($path, $ext);
      if (!$r) {
        if ($strict) {
          die("PathByFile '$path' not found<hr>".getBacktrace());
        }
        else
          return false;
      }
      else
        $existsPath = $r;
    }
    self::$paths[$path] = $existsPath;
    return $existsPath;
  }

  static function exists($path) {
    if (class_exists($path)) return true;
    return (bool)self::getPath($path, false);
  }

  static function checkExistance($path) {
    if (!self::exists($path)) throw new Exception("Class '$path' does not exists");
  }

  /**
   * Возвращает абсолютный путь к файлу по пути относительно NGN-каталога
   * либо относительно lib-каталога
   *
   * @param   string  Путь к файлу относительно NGN-каталога
   * @return  mixed   Абсолютный путь к файлу или false, если файл не найден
   */
  static function getFilePath($path, $ext = '.php') {
    foreach (Ngn::$basePaths as $basePath) {
      if (file_exists("$basePath/$path$ext")) return "$basePath/$path$ext";
    }
    return false;
  }

  static function getLocation($path) {
    if (strstr($path, LIB_PATH)) return 'core';
    elseif (strstr($path, SITE_LIB_PATH)) return 'site';
    elseif (strstr($path, NGN_PATH)) return 'ngn';
    elseif (strstr($path, VENDORS_PATH)) return 'vendor';
    else return 'other';
  }

  static protected $currentGetClass = '[none]';

  /**
   * Возвращает абсолютный путь к файлу класса
   *
   * @param   string  Имя класса
   * @return  string  Абсолютный путь к файлу или false, если файл не найден
   */
  static function getClassPath($class) {
    self::$currentGetClass = $class;
    $classesList = self::$isCache ? self::getClassesListCached() : self::getClassesList();
    if (isset($classesList[$class])) return $classesList[$class]['path'];
    return false;
  }

  /**
   * Возвращает путь до файла с классом, если имя класса выполнено
   * в стиле Zend Framework: Package_Lib_Sublib
   *
   * @return  string  Абсолютный путь к файлу или false, если файл не уществует
   */
  static protected function vendorAutoloader($class) {
    if (($pos = strpos($class, '_')) === false) return false;
    $vendor = substr($class, 0, $pos);
    if (!isset(self::$vendorAutoloaders[$vendor])) return false;
    $autoloader = self::$vendorAutoloaders[$vendor];
    $autoloader($class);
  }

  static protected $vendorAutoloaders = [];

  static function addVendorAutoloader($vendor, $func) {
    self::$vendorAutoloaders[$vendor] = $func;
  }

  static function addPearAutoloader($vendor) {
    self::$vendorAutoloaders[$vendor] = 'pearAutoload';
  }

  /**
   * Рекурсивно проходит данную директорию и возвращает массив с классами,
   * найденными в ней.
   * Структура массива:
   * array(
   *   'path' => 'absolute/path/to/file',
   *   'file' => 'ClassName.class.php'
   * )
   *
   * @param   string  Корневая директория для просмотра (не изменяется)
   * @param   string  Субдиректория
   * @return  array   Список файлов
   */
  static private function getClassesListR($rootPath, $path = null) {
    $list = [];
    $dirPath = $rootPath.($path ? '/'.$path : '');
    if (!file_exists($dirPath)) die2("Folder '$dirPath' does not exists");
    if (!is_dir($dirPath)) die2("'$dirPath' is not folder");
    foreach (Dir::getFilesR($dirPath, '*.class.php') as $file) {
      $className = Misc::removeSuffix('.class.php', basename($file));
      $new = [
        'path' => $file,
        'file' => basename($file)
      ];
      $list[$className] = $new;
    }
    return $list;
  }

  /**
   * Массив с дополнительными папками, в которых нужно искать классы.
   * При добавлении дополнительноо каталога кэш отключается, т.к. добавление
   * происходит динамически тогда, когда кэш классов уже сформирован.
   *
   * @var array
   */
  static $libFolders = [];

  static function addFolder($folder) {
    if (!file_exists($folder)) throw new NoFileException($folder);
    if (self::$listIsFormed) throw new Exception("Can not add folder after classes list already formed. Check calles of undefined classes before this Lib::addFolder call. Backtrace of first call: \n".self::$firstCallBacktrace);
    self::$libFolders[] = $folder;
  }

  static $listIsFormed = false;

  static $firstCallBacktrace;

  /**
   * Возвращает массив со списком существующих классаов
   * Структура массива:
   * array(
   *   'path' => 'absolute/path/to/file',
   *   'file' => 'ClassName.class.php'
   * )
   *
   * @return  array
   */
  static function initClassesList() {
    if (!defined('LIB_PATH')) die('Lib::getClassesList(): LIB_PATH not defined. Try to load class "'.self::$currentGetClass.'"');
    self::$list = [];
    if (defined('SITE_LIB_PATH') and file_exists(SITE_LIB_PATH)) self::loadFolder(SITE_LIB_PATH);
    self::loadFolder(LIB_PATH);
    self::loadFolder(VENDORS_PATH);
    if (isset(self::$libFolders)) foreach (self::$libFolders as $folder) self::loadFolder($folder);
    self::$listIsFormed = true;
    self::$firstCallBacktrace = getBacktrace(false);
  }

  static protected function loadFolder($path) {
    self::$list += self::getClassesListR($path);
  }

  static protected $list;

  static function getClassesList() {
    self::initClassesList();
    return self::$list;
  }

  static function getClassesListCached() {
    if (isset(self::$list)) return self::$list;
    $cache = NgnCache::c();
    if (!(self::$list = $cache->load('classesList'))) {
      self::initClassesList();
      $cache->save(self::$list, 'classesList');
    }
    self::$listIsFormed = true;
    self::$firstCallBacktrace = getBacktrace(false);
    return self::$list;
  }

  static function requireFolder($name) {
    foreach (glob(LIB_PATH."/more/$name/*.php") as $file) {
      if (Misc::hasPrefix('Test', basename($file))) continue;
      require_once $file;
    }
  }

}