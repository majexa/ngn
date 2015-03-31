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

  static protected $cacheEnabled = false;

  static $cl = [];

  /**
   * Подключает файл по пути относительно корня NGN-каталога, либо по имени класса
   *
   * @param string $_path Путь к файлу относительно корня NGN-каталога или имя класса
   * @param Req $req
   * @param string $ext
   * @return bool
   */
  static function required($_path, Req $req = null, $ext = '.php') {
    if (!in_array($_path, self::$cl)) self::$cl[] = $_path;
    if (self::vendorAutoloader($_path) !== false) return false;
    if (($path = self::getPath($_path, false, $ext)) == false) return false;
    if ($req) $_REQUEST = $req->r;
    require_once $path;
    return true;
  }

  static $paths = [];

  /**
   * Получает путь к файлу.
   * $path может быть:
   * - ClassName
   * - classLib/ClassName
   * - ngn/file.php
   * - ngn/lib/file.php
   * - site/lib/file.php
   *
   * @param $path
   * @param bool $strict
   * @param string $ext
   * @return bool|mixed|string
   */
  static function getPath($path, $strict = true, $ext = '.php') {
    if (isset(self::$paths) and isset(self::$paths[$path])) return self::$paths[$path];
    $existingPath = false;
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
        $existingPath = $r;
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
        $existingPath = $r;
    }
    self::$paths[$path] = $existingPath;
    return $existingPath;
  }

  static function exists($path) {
    if (class_exists($path)) return true;
    return (bool)self::getPath($path, false);
  }

  static function checkExistance($path) {
    if (!self::exists($path)) throw new Exception("Class '$path' does not exists");
  }

  /**
   * Возвращает абсолютный путь к файлу по пути относительно NGN-каталога либо относительно lib-каталога
   *
   * @param string $path Путь к файлу относительно NGN-каталога
   * @param string $ext
   * @return bool|string
   */
  static function getFilePath($path, $ext = '.php') {
    foreach (Ngn::$basePaths as $basePath) {
      if (file_exists("$basePath/$path$ext")) return "$basePath/$path$ext";
    }
    return false;
  }

  static function getLocation($path) {
    if (strstr($path, CORE_PATH)) return 'core';
    elseif (strstr($path, MORE_PATH)) return 'more';
    elseif (strstr($path, SITE_LIB_PATH)) return 'site';
    elseif (strstr($path, NGN_PATH)) return 'ngn';
    elseif (strstr($path, VENDORS_PATH)) return 'vendor';
    else return 'other';
  }

  static protected $currentGetClass = '[none]';

  /**
   * Возвращает абсолютный путь к файлу или false, если файл не найден
   *
   * @param   string $class Имя класса
   * @return  string|false
   */
  static function getClassPath($class) {
    self::$currentGetClass = $class;
    $classesList = self::getClassesListCached($class);
    if (isset($classesList[$class])) return $classesList[$class]['path'];
    return false;
  }

  /**
   * Возвращает Абсолютный путь к файлу с классом или false, если файл не уществует
   * Имя класса должно быть выполнено в стиле Zend Framework: Package_Lib_Sublib
   *
   * @param $class
   * @return bool
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
   * Рекурсивно проходит данную директорию и возвращает массив с классами, найденными в ней и возвращает вписок файлов
   *
   * Структура массива:
   * array(
   *   'path' => 'absolute/path/to/file',
   *   'file' => 'ClassName.class.php'
   * )
   *
   * @param   string $rootPath Корневая директория для просмотра (не изменяется)
   * @param   string $path Субдиректория
   * @return  array
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
    if (!in_array($folder, self::$libFolders)) self::$libFolders[] = $folder;
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
    self::$list = [];
    if (isset(self::$libFolders)) foreach (self::$libFolders as $folder) self::loadFolder($folder);
    if (defined('SITE_LIB_PATH') and file_exists(SITE_LIB_PATH)) self::loadFolder(SITE_LIB_PATH);
    self::$listIsFormed = true;
    self::$firstCallBacktrace = getBacktrace(false);
  }

  static protected function loadFolder($path) {
    self::$list += self::getClassesListR($path);
  }

  static $list = false;

  /**
   * Возвращает список всех существующих классов в формате [name => pathToFile]
   *
   * @return array
   */
  static function getClassesList() {
    self::initClassesList();
    return self::$list;
  }

  static protected $cachePrefix;

  /**
   * Включает кэширование библиотек. До вызова этой функции нельзя использовать
   * неподгруженные (require/include) классы.
   *
   * @param string $key Уникальный ключ кэширования
   * @throws Exception
   */
  static function enableCache($key = null) {
    if (self::$cacheEnabled) throw new Exception('Lib cache already enabled');
    if ($key) self::$cachePrefix = Misc::shortHash($key);
    self::$cacheEnabled = true;
  }

  static function getClassesListCached($forClass = null) {
    if (!self::$cacheEnabled) throw new Exception('Please enable cache [Lib::enableCache()] before use class '.($forClass ? '"'.$forClass.'" ' : '').'autoload');
    if (self::$list !== false) return self::$list;
    $options = [];
    if (isset(self::$cachePrefix)) $options['file_name_prefix'] = self::$cachePrefix;
    $cache = FileCache::c($options);
    if (!(self::$list = $cache->load('classesList'))) {
      self::initClassesList();
      $cache->save(self::$list, 'classesList');
    }
    self::$listIsFormed = true;
    self::$firstCallBacktrace = getBacktrace(false);
    return self::$list;
  }

}