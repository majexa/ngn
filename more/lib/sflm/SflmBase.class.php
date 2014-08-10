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
    $this->packagesCache[$lib] = $r;
    Sflm::output("Got package '$lib' libs recursive: ".implode(", ", $this->packagesCache[$lib]));
    return $this->packagesCache[$lib];
  }

  protected function getPackageLibs($package, $strict = false) {
    if (($r = Config::getVar("sfl/".$this->type."/$package", true, false)) !== false) return $r;
    $errText = "Package '$package' (sfl/".$this->type."/$package) does not exists";
    if (!$strict) {
      Sflm::output($errText);
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
        if (!file_exists($absPath)) Sflm::output("File '$absPath' does not exists");
        $r[] = $lib;
      }
    }
    return $r;
  }

  // --

  public $type, $version = 1;

  protected function getFileContents($path, $strict = true, $r = []) {
    Misc::checkEmpty($path);
    if (!is_file($path)) {
      $error = "File '$path' does not exists";
      Sflm::output($error);
      if ($strict) Err::_log($error, debug_backtrace());
      return "\n/*--[$error]--*/\n";
    }
    if (strstr($path, '/scripts/')) {
      // Если файл находится в папке библиотек, значит это PHP-файл
      return "\n/*--|$path|".($r ? ' (with request data)' : '')."--*/\n".Misc::getIncludedByRequest($path, $r);
    }
    else {
      // Иначе это статика
      return "\n/*--|$path|--*/\n".file_get_contents($path);
    }
  }

  function getPackageCode($package) {
    return $this->extractCode($this->getPaths($package));
  }

  protected function isStrictPath($path) {
    return !Misc::hasPrefix('m/', $path);
  }

  function extractCode(array $paths) {
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
    return UPLOAD_PATH.'/'.$this->filePath($package);
  }

  function storeLib($package, $code = null) {
    $file = $this->cacheFile($package);
    if (!$code) $code = $this->getPackageCode($package);
    Misc::checkEmpty($code, "No code in package [$this->type::$package]");
    if (file_exists($file) and file_get_contents($file) == $code) return false; // Если размер кода не изменился, не сохраняем
    Dir::make(UPLOAD_PATH.'/'.$this->type.'/cache');
    file_put_contents($file, $code);
    return true;
  }

  function getCode($package) {
    if (Sflm::$debug or Sflm::$forceCache or !file_exists(UPLOAD_PATH.'/'.$this->type.'/cache/'.$package.'.css')) {
      // Если идёт отладка статических файлов или собранного файла не существует
      $this->storeLib($package);
    }
    return file_get_contents(UPLOAD_PATH.'/'.$this->type.'/cache/'.$package.'.'.$this->type);
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
      die2("\n111");
      return (bool)Config::getVar("sfl/".$this->type."/$lib", true);
    }
    else {
      die2("\n222");
      return file_exists($this->getAbsPath($lib));
    }
  }

  function getAbsPath($path) {
    if ($this->isPackage($path)) throw new Exception("It ($path) can not be package");
    $p = parse_url($path);
    $path = $p['path'];
    foreach (Sflm::$absBasePaths as $package => $absBasePath) {
      if (preg_match('/^\/?'.$package.'\//', $path)) {
        //if (strstr($path, 'Ngn.Request')) die2([$package, $path]);
        return "$absBasePath/".Misc::removePrefix("$package/", ltrim($path, '/'));
      }
    }
    $prefix = explode('/', $path)[0];
    if (in_array($prefix, RouterScripts::prefixes())) return $this->getScriptPath($path);
    throw new Exception("Unexpected prefix '$prefix' in path '$path'");
  }

  /**
   * Возвращает абсолютный путь до скрипта
   *
   * @param string $path Пример: "s2/css/icons.css"
   * @return bool|string
   * @throws Exception
   */
  function getScriptPath($path) {
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
    $path = $this->getScriptPath($path);
    if (Sflm::$forceCache or !file_exists(UPLOAD_PATH.'/'.$cachePath)) {
      Dir::make(UPLOAD_PATH.'/'.dirname($cachePath));
      if (!empty($p['query'])) {
        parse_str($p['query'], $q);
        if (!empty($q)) {
          $cachePath = Misc::getFilePrefexedPath($cachePath, Tt()->enum($q, '-', '$k.`,`.$v').'.');
        }
      }
      else {
        $q = [];
      }
      file_put_contents(UPLOAD_PATH.'/'.$cachePath, Misc::getIncludedByRequest($path, $q));
    }
    return UPLOAD_DIR.'/'.$cachePath;
  }

  protected function getCachePath($path) {
    $path2 = str_replace('s2/', '', $path);
    return $this->type.'/cache/'.basename($path2).(strstr($path2, '.') ? '' : $this->type);
  }

  function clearPathCache($path) {
    File::delete(UPLOAD_PATH.'/'.$this->getCachePath($path));
  }

  function getPackagePath($package) {
    return Arr::last(Config::getFilePaths("sfl/".$this->type."/$package", 'vars'));
  }

  public $packagesCache = [];

  protected $existingPackages = [];

  //protected function processPathOnAdd($path) {
  //}195


  function isPackage($path) {
    return !(strstr($path, '.') or strstr($path, $this->type.'/'));
  }

  function getTags($package, $code = null) {
    if (Sflm::$debug) {
      $t = '';
      foreach ($this->getPaths($package) as $path) {
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
    if ($force or Sflm::$debug or Sflm::$forceCache or !file_exists(UPLOAD_PATH.'/'.$this->filePath($package))) {
      // Если идёт отладка статических файлов или собранного файла не существует
      $this->storeLib($package, $code);
    }
    return '/'.UPLOAD_DIR.'/'.$this->filePath($package).'?'.$this->version;
  }

}
