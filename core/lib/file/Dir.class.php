<?php

class Dir {

  static function make($path) {
    if (!strstr($path, '/') and !strstr($path, '\\')) Err::warning('Use absolute dir path. $path='.$path);
    if (!$existsPath = self::getExists($path)) throw new Exception("Can not detect existing parent path by '$path'. Not writable?");
    $extraPath = substr($path, strlen($existsPath), strlen($path));
    $folders = explode('/', Misc::clearLastSlash($extraPath));
    foreach ($folders as $folder) {
      $existsPath .= '/'.$folder;
      if (!is_dir($existsPath)) self::makeDir($existsPath);
    }
    return Misc::clearLastSlash($existsPath);
  }

  static private function makeDir($path) {
    mkdir($path) or Err::error("Making '$path'");
    chmod($path, 0777);
  }

  /**
   * Возвращает существующий путь к директории проходя вверх по данной директории
   *
   * @param   string  Путь к предполагаемой существующей директории
   * @return  mixed   false если таковой директории нет, и путь до директории,
   *                  если она найдена
   */
  static function getExists($_path) {
    $path = $_path;
    while (!is_dir($path) and $path != '/') $path = dirname($path);
    if (!is_writable($path)) return false;
    return $path == '.' ? false : $path;
  }

  static function isEmpty($path) {
    if (!is_dir($path)) return false;
    $d = dir($path);
    while (false !== ($entry = $d->read())) {
      if ($entry != '.' and $entry != '..') {
        return false;
      }
    }
    $d->close();
    return true;
  }

  static function removeIfEmpty($dirname) {
    if (!file_exists($dirname)) return;
    if (!glob($dirname.'/*')) rmdir($dirname);
  }

  /**
   * Рекурсивно удаляет директорию
   *
   * @param string $dirname
   * @param bool $removeItself
   * @return bool
   * @throws Exception
   */
  static function remove($dirname, $removeItself = true) {
    if (!file_exists($dirname)) return false;
    if (is_file($dirname) or is_link($dirname)) {
      return unlink($dirname);
    }
    if (!$dir = dir($dirname)) throw new Exception("Check permissions for $dirname", true);
    while (false !== $entry = $dir->read()) {
      if ($entry == '.' or $entry == '..') continue;
      self::remove("$dirname/$entry");
    }
    $dir->close();
    if ($removeItself) rmdir($dirname);
  }

  /**
   * Удаляет содержимое директории
   *
   *
   */
  static function clear($dirname) {
    self::remove($dirname, false);
  }

  static function getFiles_noCache($dirPath, $quietly = false) {
    if (!file_exists($dirPath)) {
      if ($quietly) return [];
      else throw new Exception("Folder '$dirPath' not exists");
    }
    $files = [];
    $d = dir($dirPath);
    while (($entry = $d->read())) {
      if (is_file($dirPath.'/'.$entry)) {
        $files[] = $entry;
      }
    }
    $d->close();
    sort($files);
    return $files;
  }

  static $nnn = 0;

  private static $pattern;

  static function setPattern($pattern) {
    $pattern = str_replace('\\', '\\\\', $pattern); // win fix
    $pattern = str_replace('.', '\\.', $pattern);
    $pattern = str_replace('*', '.*', $pattern);
    $pattern = str_replace('/', '\/', $pattern);
    self::$pattern = $pattern;
  }

  static function getFilesR($dirPath, $pattern = '*') {
    return self::_getFiles($dirPath, true, $pattern);
  }

  static function getFlat($dirPath) {
    $r = glob($dirPath.'/*');
    $files = [];
    foreach ($r as $entry) {
      if ($entry == '.' or $entry == '..') continue;
      $files[] = $entry;
    }
    return $files;
  }

  /**
   * Удаляет файлы по маске
   *
   * @param   string  Путь к каталогу
   * @param   string  Маска
   */
  static function deleteFiles($dirPath, $pattern) {
    foreach (self::getFilesR($dirPath, $pattern) as $file) unlink($file);
  }

  static protected $includingDirs = false;

  const FILE = 1;
  const DIR = 2;

  static function _getFiles($dirPath, $recursive = true, $pattern = '*', $mode = self::FILE) {
    self::setPattern($dirPath.'/'.$pattern);
    $dirPath = Misc::clearLastSlash($dirPath);
    $files = [];
    if (($r = glob($dirPath.'/*')) === false) return $files;
    foreach ($r as $entry) {
      if ((is_file($entry) and $mode == self::FILE) or (is_dir($entry) and $mode == self::DIR)) {
        if (self::$pattern == '*' or preg_match('/^'.self::$pattern.'$/', $entry)) $files[] = $entry;
      }
    }
    if ($recursive) {
      foreach ($r as $entry) {
        if (is_dir($entry)) {
          $files = Arr::append($files, self::_getFiles($entry, $recursive, $pattern, $mode));
        }
      }
    }
    return $files;
  }

  static function _getDirs($dirPath, $recursive = true, $pattern = '*') {
    return self::_getFiles($dirPath, $recursive, $pattern, self::DIR);
  }

  /**
   * @depricated
   */
  static function get($dirPath) {
    return glob($dirPath.'/*');
  }

  /**
   * @depricated
   */
  static function dirs($dirPath) {
    return glob($dirPath.'/*', GLOB_ONLYDIR);
  }

  static function dirsDetail($dirPath) {
    $_dirs = [];
    foreach (self::dirs($dirPath) as $dir) {
      $files = self::files($dirPath.'/'.$dir);
      $size = 0;
      foreach ($files as $file) $size += filesize($dirPath.'/'.$dir.'/'.$file);
      $_dirs[] = [
        'path'  => $dirPath.'/'.$dir,
        'name'  => $dir,
        'files' => count($files),
        'size'  => $size
      ];
    }
    return $_dirs;
  }

  static function isDirs($dirPath) {
    $d = dir($dirPath);
    while (($entry = $d->read())) {
      if ($entry == '.' or $entry == '..') {
        continue;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        $d->close();
        return true;
      }
    }
    $d->close();
    return false;
  }

  static function files($dirPath, $quietly = false) {
    return self::getFiles_noCache($dirPath, $quietly);
  }

  static function everyNFile($dirPath, $from, $n) {
    $entrys = self::files($dirPath);
    sort($entrys);
    $filesStep = round((count($entrys) - $from) / $n);
    if ($filesStep > 1) {
      $firstI = $from;
      $i = $firstI;
      while ($i < $firstI + ($filesStep * $n)) {
        $files[] = $entrys[$i];
        $i += $filesStep;
      }
    }
    return $files;
  }

  static function getFiles($dirPath, $quietly = false) {
    /*
     * @depriceted:
    if (IS_MEMCACHED === true) {
      if (($files = DirMem::get(str_replace('/', '_', $dirPath)))) return $files;
      $files = self::getFiles_noCache($dirPath, $quietly);
      DirMem::set(str_replace('/', '_', $dirPath), $files, 60);
      return $files;
    }
    */
    return self::getFiles_noCache($dirPath, $quietly);
  }

  static function copy_($soursePath, $destPath) {
    if (!is_dir($soursePath)) throw new Exception("Sourse folder '$destPath' not exists");
    if (!is_dir($destPath)) throw new Exception("Destination folder '$destPath' not exists");
    if (getOS() == 'unix1') {
      system("cp -r $soursePath $destPath", $error);
      if ($error) throw new Exception("Can't copy dir '$soursePath' to '$destPath' "."(check permissions for $destPath)");
    }
    else {
      self::copyPhp($soursePath, $destPath);
    }
  }

  static function copy($dir1, $dir2, $replace = true) {
    if ($replace and file_exists($dir2)) self::remove($dir2);
    if (!file_exists($dir2)) self::make($dir2);
    self::copyPhpContents($dir1, $dir2);
  }

  static $nonCopyNames = [
    '.svn'
  ];

  static $replaceExistsFolders = true;

  static function copyPhpContents($dir1, $dir2) {
    Misc::clearLastSlash($dir2);
    if (!is_dir($dir2)) throw new Exception("Dir '$dir2' not exists", true);
    if (($dh = opendir($dir1)) !== false) {
      $i = 0;
      while (($el = readdir($dh)) !== false) {
        // Если имя каталога-файла в числе тех, что не нужно копировать
        if (in_array($el, self::$nonCopyNames)) continue;
        $path1 = $dir1.'/'.$el;
        $path2 = $dir2.'/'.$el;
        if (strstr($dir2, $path1)) continue; // нельзя копировать ту папку, в которую копируем
        if (is_dir($path1) && $el != '.' && $el != '..') {
          if (!self::$replaceExistsFolders and is_dir($path2)) continue;
          if (!file_exists($path2)) mkdir($path2);
          //if (!mkdir($path2)) throw new Exception("Cant make '$path2'. Already exists");
          self::copyPhpContents($path1, $path2);
        }
        elseif (is_file($path1)) {
          if (!copy($path1, $path2)) {
            throw new Exception('Could not copy file, '.$path1.', to '.$path2);
          }
        }
        $i++;
      }
      closedir($dh);
      return true;
    }
    else {
      throw new Exception('Could not open the directory "'.$dir1.'"');
    }
  }

  static function _getSize($path) {
    $size = 0;
    if (!$d = dir($path)) throw new Exception('Could not open the directory "'.$path.'"');
    while (false !== $entry = $d->read()) {
      if ($entry == '.' or $entry == '..') {
        continue;
      }
      if (is_dir($path.'/'.$entry)) $size += self::_getSize($path.'/'.$entry);
      else
        $size += filesize($path.'/'.$entry);
    }
    return $size;
  }

  static function _getSizeSys($path) {
    $r = explode("\n", trim(`du $path`));
    list($kb) = explode("\t", $r[count($r) - 1]);
    return $kb * 1000;
  }

  static function getSize($path, $lifetime = 43200) {
    $key = str_replace(['/', '.', '-'], '_', str_replace(':', '', $path));
    if (($r = FileCache::c([
        'lifetime' => $lifetime
      ])->load($key)) === false
    ) {
      $r = self::_getSizeSys($path);
      FileCache::c()->save($r, $key);
    }
    return $r;
  }

  private static $modifTime = 0;

  static $lastModifExcept = [];

  static private function setLastModifTimeR($dirPath) {
    $d = dir($dirPath);
    while (($entry = $d->read()) !== false) {
      if ($entry == '.' or $entry == '..') {
        continue;
      }
      if (in_array($entry, self::$lastModifExcept)) continue;
      if (!$mtime = filemtime($dirPath.'/'.$entry)) return false;
      if ($mtime > self::$modifTime) {
        self::$modifTime = $mtime;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        self::setLastModifTimeR($dirPath.'/'.$entry);
      }
    }
    $d->close();
    self::$modifTime;
  }

  static function getLastModifTime($dirPath) {
    self::setLastModifTimeR($dirPath);
    return self::$modifTime;
  }

  static function moveContents($from, $to, $replace = true) {
    if (!is_dir($from)) throw new Exception("'$from' is not dir");
    $from = Misc::clearLastSlash($from);
    $to = Misc::clearLastSlash($to);
    $d = dir($from);
    while (($entry = $d->read()) !== false) {
      if ($entry == '.' or $entry == '..') {
        continue;
      }
      $pathTo = ($to ? $to.'/' : '').$entry;
      self::remove($pathTo);
      rename(($from ? $from.'/' : '').$entry, $pathTo);
    }
  }

  static function move($from, $to, $replace = true) {
    if ($replace) {
      output("Replace folder '$to' by '$from'");
    }
    else {
      output("Move folder '$from' by '$to'");
    }
    if ($replace and file_exists($to)) {
      if ($to == '.' or $to == '') throw new Exception("Try to remove current dir '$to'");
      self::remove($to);
    }
    self::make($to);
    self::moveContents($from, $to);
    self::remove($from);
  }

  static function chmod($dir, $prem) {
    chmod($dir, $prem);
    foreach (self::get($dir) as $name) {
      $path = $dir.'/'.$name;
      if (is_dir($path)) {
        chmod($path, $prem);
        self::chmod($path, $prem);
      }
      else
        chmod($path, $prem);
    }
  }

  static function renameFileFolderContents($folder, $find, $replace) {
    output("Rename files, folders and their contents in '$folder' folder from '$find' to '$replace'");
    self::$includingDirs = true;
    foreach (self::getFilesR($folder) as $v) {
      $file = basename($v);
      if (is_file($v) and File::findContents($v, $find)) {
        File::replace($v, $find, $replace);
        output("Replace contents in '$v'");
      }
      if (strstr($file, $find)) {
        rename($v, dirname($v).'/'.str_replace($find, $replace, $file));
        output("Rename filename '$v'");
      }
    }
  }

  static function getOrderedFiles($folder, $globPattern = '*') {
    $files = glob("$folder/$globPattern");
    if (file_exists("$folder/order")) {
      foreach ($files as $v) $files2[basename($v)] = $v;
      return array_values(Arr::sortByArray($files2, File::strings("$folder/order")));
    }
    return $files;
  }

  /**
   * Удаляет пустые папки в указанной папке
   *
   * @param string Папка для поиска поддиректорий
   */
  static function removeEmpties($folder) {
    foreach (glob($folder.'/*') as $v) self::removeIfEmpty($v);
  }

}