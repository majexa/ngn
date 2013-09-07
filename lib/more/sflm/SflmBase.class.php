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

  public $type, $version = 1;

  protected function getFileContents($path, $r = []) {
    if (!is_file($path)) {
      $error = "File '$path' does not exists";
      //Err::_log($error, debug_backtrace());
      return "\n/*----------[ $error ]---------*/\n";
    }
    if (strstr($path, '/scripts/')) {
      // Если файл находится в папке библиотек, значит это PHP-файл
      return "\n/*----------|$path|".($r ? ' (with request data)' : '')."----------*/\n".Misc::getIncludedByRequest($path, $r);
    }
    else {
      // Иначе это статика
      return "\n/*----------|$path|----------*/\n".file_get_contents($path);
    }
  }

  protected function getPackageCode($package) {
    return $this->extractCode($this->getPaths($package));
  }

  function extractCode(array $paths) {
    $code = '';
    foreach ($paths as $path) {
      $absPath = $this->getAbsPath($path);
      $p = parse_url($path);
      if (!empty($p['query'])) {
        $a = [];
        parse_str($p['query'], $a);
        $code .= $this->getFileContents($absPath, $a);
      }
      else {
        $code .= $this->getFileContents($absPath);
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
    Misc::checkEmpty($code, '$code');
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

  function getAbsPath($path) {
    $p = parse_url($path);
    $path = $p['path'];
    foreach (Sflm::$absBasePaths as $package => $absBasePath) {
      if (preg_match('/^\/?'.$package.'\//', $path)) {
        return "$absBasePath/".Misc::removePrefix("$package/", ltrim($path, '/'));
      }
    }
    return $this->getScriptPath($path);
  }

  function getPath($absPath) {
    foreach (Sflm::$absBasePaths as $folder => $absBasePath) {
      if (Misc::hasPrefix($absBasePath, $absPath)) {
        return $folder.Misc::removePrefix($absBasePath, $absPath);
      }
    }
    throw new NotFoundException($absPath);
  }

  /**
   * Возвращает абсолютный путь до скрипта
   *
   * @param   string  Пример: "s2/css/icons.css"
   */
  function getScriptPath($path) {
    $path = Misc::clearFirstSlash($path);
    if (preg_match('/^s2\/(.*)/', $path)) {
      $path2 = preg_replace('/^s2\/(.*)/', '$1', $path);
      foreach ($this->pathVariants($path2) as $_path) if (($__path = Lib::getFilePath($_path, ''))) return $__path;
      throw new Exception('Script by path "'.$path2.'" not found');
    }
    return $path;
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

  protected function getPackageLibs($package) {
    return Config::getVar("sfl/".$this->type."/$package");
  }

  protected $libsCache = [];

  function getPaths($lib) {
    if (!$this->isPackage($lib)) return [$lib];
    if (isset($this->libsCache[$lib])) return $this->libsCache[$lib];
    output("Getting package '$lib' libs recursive");
    return $this->libsCache[$lib] = $this->getPackageLibsR($lib);
  }

  protected function getPackageLibsR($package) {
    $libs = [];
    foreach ($this->getPackageLibs($package) as $lib) {
      if ($this->isPackage($lib)) $libs = Arr::append($libs, $this->getPackageLibsR($lib));
      else $libs[] = $lib;
    }
    return $libs;
  }

  protected function isPackage($path) {
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
    return $this->type.'/cache/'.$package.'.'.$this->type;
  }

  function getUrl($package, $code = null) {
    if (Sflm::$debug or Sflm::$forceCache or !file_exists(UPLOAD_PATH.'/'.$this->filePath($package))) {
      // Если идёт отладка статических файлов или собранного файла не существует
      $this->storeLib($package, $code);
    }
    return '/'.UPLOAD_DIR.'/'.$this->filePath($package);
  }

}
