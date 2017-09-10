<?php

/**
 * Static File Libraries Manager
 *
 * path - путь. (i/path/to/file). путь к файлу относильно web-рута
 * package - пакет. имя набора библиотек
 * lib - библиотека. путь или пакет
 *
 */
abstract class SflmBase {

  protected $absPathsCache = [];
  protected $extractCodeCache;

  function __construct() {
    if (($r = FileCache::c()->load('sflm'.$this->type.'absPaths')) !== false) {
      $this->absPathsCache = $r;
    }
  }

  /**
   * Возвращает массив путей для указанного пакета, за исключением подпакетов, уже находящихся в кэше.
   * Или сам путь (если указан путь), обёрнутый в массив
   *
   * @param string $lib
   * @param bool $strict
   * @return array
   */
  function getPaths($lib, $strict = false) {
    if (!$this->isPackage($lib)) return [$lib];
    if (isset($this->packagesCache[$lib])) return $this->packagesCache[$lib];
    if (($r = $this->getPackageLibsR($lib, true, $strict)) === false) return $this->packagesCache[$lib] = [];
    $this->packagesCache[$lib] = array_unique($r);
    if ($this->packagesCache[$lib]) {
      Sflm::log("Got package '$lib' libs recursive: ".implode(", ", $this->packagesCache[$lib]));
    } else {
      Sflm::log("Package '$lib' is empty");
    }
    return $this->packagesCache[$lib];
  }

  /**
   * @param $package
   * @param bool $strict
   * @returns array|false
   * @throws Exception
   */
  protected function getPackageLibs($package, $strict = false) {
    if (($r = Config::getVar("sfl/".$this->type."/$package", true, false)) !== false) {
      return $r;
    }
    $errText = "Package '$package' (sfl/".$this->type."/$package) does not exists";
    if (!$strict) {
      Sflm::log($errText);
      return false;
    } else {
      throw new Exception($errText);
    }
  }

  protected function getPackageLibsR($package, $skipExistingPackages = true, $strict = false) {
    if (($libs = $this->getPackageLibs($package, $strict)) === false) return false;
    $r = [];
    $this->existingPackages[] = $package;
    foreach ($libs as $lib) {
      if ($this->isPackage($lib)) {
        if ($skipExistingPackages and in_array($lib, $this->existingPackages)) continue;
        $this->existingPackages[] = $lib;
        if (($subLibs = $this->getPackageLibsR($lib, $skipExistingPackages, $strict)) === false) continue;
        $r = Arr::append($r, $subLibs);
      }
      else {
        if (Misc::hasPrefix('i/', basename($lib))) {
          File::checkExists($this->getAbsPath($lib), "Path '$lib' in package '$package' does not exists");
        }
        $absPath = $this->getAbsPath($lib);
        if (!file_exists($absPath)) Sflm::log("File '$absPath' does not exists");
        $r[] = $lib;
      }
    }
    return $r;
  }

  // --

  public $type, $version = 1;

  function getFileContents($path, $strict = true, $r = []) {
    Misc::checkEmpty($path);
    if (!is_file($path)) {
      $error = "File '$path' does not exists";
      Sflm::log($error);
      if ($strict) Err::_log($error, debug_backtrace());
      return "\n/*--[$error]--*/\n";
    }
    if (strstr($path, '/scripts/')) {
      // Если файл находится в папке библиотек, значит это PHP-файл
      return "\n/*--|$path|".($r ? ' (with request data)' : '')."--*/\n".Misc::getIncludedByRequest($path, $r);
    }
    else {
      // Иначе это статика
      return "\n/*--|$path|--*/\n".$this->getContents($path);
    }
  }

  protected function getContents($path) {
    return file_get_contents($path);
  }

  function getPackageCode($package) {
    return $this->extractCode($this->getPaths($package));
  }

  protected function isStrictPath($path) {
    return !Misc::hasPrefix('m/', $path);
  }

  function extractCode(array $paths, $key = null) {
    $code = '';
    foreach ($paths as $path) {
      $absPath = $this->getAbsPath($path);
      $p = parse_url($path);
      if (!empty($p['query'])) {
        $a = [];
        parse_str($p['query'], $a);
        $code .= $this->getFileContents($absPath, $this->isStrictPath($path), $a);
      }
      else {
        $code .= $this->getFileContents($absPath, $this->isStrictPath($path));
      }
    }
    return $code;
  }

  function cacheFile($package) {
    Dir::make(Sflm::$webPath.'/'.$this->type.'/cache');
    return Sflm::$webPath.'/'.$this->filePath($package);
  }

  /**
   * Создаёт веб-кэш файл с записанным в него кодом
   *
   * @param string $package Имя пакета
   * @param null|string $code null если код нужно получить автоматически по имени пакета, или string для прямой записи кода
   * @return bool|string FALSE если запись не произошла, иначе путь к файлу
   * @throws Exception
   */
  function storeLib($package, $code = null) {
    $file = $this->cacheFile($package);
    if (!$code) $code = $this->getPackageCode($package);
    if (!$code) return false;
    // Если код не изменился, не сохраняем
    if (file_exists($file) and md5(file_get_contents($file)) == md5($code)) return false;
    file_put_contents($file, $code);
    return $file;
  }

  function getCode($package) {
    if (Sflm::$debug or Sflm::$forceCache or !file_exists(Sflm::$webPath.'/'.$this->type.'/cache/'.$package.'.css')) {
      // Если идёт отладка статических файлов или собранного файла не существует
      $this->storeLib($package);
    }
    return file_get_contents(Sflm::$webPath.'/'.$this->type.'/cache/'.$package.'.'.$this->type);
  }

  function exists($lib) {
    if ($this->isPackage($lib)) {
      return (bool)Config::getVar("sfl/".$this->type."/$lib", true);
    }
    else {
      return file_exists($this->getAbsPath($lib));
    }
  }

  function where($lib) {
    if ($this->isPackage($lib)) {
      return (bool)Config::getVar("sfl/".$this->type."/$lib", true);
    }
    else {
      return file_exists($this->getAbsPath($lib));
    }
  }

  /**
   * @api
   * Возвращает абсолютный путь к sf-ресурсу. sf-ресурсу это CSS/JS файл доступный через HTTP
   * Т.е. если sflm-фронтенд используется на сайте site.com, то русурс $path должен быть доступен
   * по ссылке http://site.com/$path
   *
   * @param string $_path Путь к sf-русурсу
   * @return string
   * @throws Exception
   */
  function getAbsPath($_path) {
    if ($this->isPackage($_path)) throw new Exception("It ($_path) can not be package");
    $path = parse_url($_path)['path'];
    foreach (Sflm::$absBasePaths as $package => $absBasePath) {
      if (preg_match('/^\/?'.$package.'\//', $path)) {
        $r = "$absBasePath/".Misc::removePrefix("$package/", ltrim($path, '/'));
        return $r;
      }
    }
    $prefix = explode('/', $path)[0];
    if (!$prefix) throw new Exception("Prefix is empty. Path: $_path");
    if (in_array($prefix, RouterScripts::prefixes())) {
      $r = $this->getScriptAbsPath(Misc::removeSuffix('.php', $path));
      return $r;
    }
    throw new Exception("Unexpected prefix '$prefix' in path '$path'. Use Sflm::\$absBasePaths[prefix] = '/path/to/files' to register it in your init.php file.");
  }



  /**
   * Возвращает абсолютный путь до скрипта
   *
   * @param string $path Пример: "s2/css/icons.css"
   * @return bool|string
   * @throws Exception
   */
  function getScriptAbsPath($path) {
    $path = Misc::clearFirstSlash($path);
    if (preg_match('/^s2\/(.*)/', $path)) {
      $path2 = preg_replace('/^s2\/(.*)/', '$1', $path);
      if (!($r = Lib::getFilePath("scripts/$path2"))) throw new Exception('Script by path "'.("scripts/$path2").'" not found');
      return $r;
    }
    throw new Exception("Script path '$path' not found");
  }

  function pathVariants($path) {
    return [
      "scripts/$path",
      "scripts/$path.php",
      "scripts/$path.".$this->type
    ];
  }

  function getCachedUrl($path) {
    if (Sflm::$debug) return $path;
    $p = parse_url($path);
    $path = $p['path'];
    $cachePath = $this->getCachePath($path);
    $path = $this->getScriptAbsPath($path);
    if (Sflm::$forceCache or !file_exists(Sflm::$webPath.'/'.$cachePath)) {
      Dir::make(Sflm::$webPath.'/'.dirname($cachePath));
      if (!empty($p['query'])) {
        parse_str($p['query'], $q);
        if (!empty($q)) {
          $cachePath = Misc::getFilePrefexedPath($cachePath, St::enum($q, '-', '$k.`,`.$v').'.');
        }
      }
      else {
        $q = [];
      }
      file_put_contents(Sflm::$webPath.'/'.$cachePath, Misc::getIncludedByRequest($path, $q));
    }
    return '/'.UPLOAD_DIR.'/'.$cachePath;
  }

  protected function getCachePath($path) {
    $path2 = str_replace('s2/', '', $path);
    return $this->type.'/cache/'.basename($path2).(strstr($path2, '.') ? '' : '.'.$this->type);
  }

  function clearPathCache($path) {
    File::delete(Sflm::$webPath.'/'.$this->getCachePath($path));
  }

  function getPackagePath($package) {
    return Arr::last(Config::getFilePaths("sfl/".$this->type."/$package", 'vars') ?: []);
  }

  public $packagesCache = [];

  protected $existingPackages = [];

  function isPackage($path) {
    return !(strstr($path, '.') or strstr($path, $this->type.'/'));
  }

  function getTags($package, $code = null) {
    if (Sflm::$debug) {
      $t = '';
      foreach ($this->getPaths($package) as $path) {
        $path = '/'.$path;
        if ($this->isPackage($path)) {
          $t .= $this->getTags($path);
        }
        else {
          $t .= $this->getTag($path);
        }
      }
      return $t;
    }
    else {
      return $this->getTag($this->getUrl($package, $code));
    }
  }

  abstract function getTag($path);

  function filePath($package) {
    return $this->type.'/cache/'.str_replace('/', '-', $package).'.'.$this->type;
  }

  function getUrl($package, $code = null, $force = false) {
    // Если BUILD_MODE выключен, ничего не сторим
    if (Sflm::$buildMode) {
      if ($force or Sflm::$debug or Sflm::$forceCache or !file_exists(Sflm::$webPath.'/'.$this->filePath($package))) {
        // Если идёт отладка статических файлов или собранного файла не существует
        $this->storeLib($package, $code);
      }
    }
    return '/'.UPLOAD_DIR.'/'.$this->filePath($package).'?'.$this->version;
  }

}
