<?php

class Config {

  static $tab = "  ";

  static function regexp($func, $k) {
    return str_replace('{f}', $func, str_replace('{k}', $k, '/{f}\s*\(\s*[\'"]{k}["\'],\s*(.*)\s*\)\s*;/i'));
  }

  static function clearQuotes($v) {
    return preg_replace('/[\'"]?(.*)[\'"]?/U', '$1', $v);
  }

  static function __getConstant($c, $k) {
    if (preg_match(self::regexp('define', $k), $c, $m)) {
      return self::clearQuotes($m[1]);
    }
    elseif (preg_match(self::regexp('setConstant', $k), $c, $m)) {
      return self::clearQuotes($m[1]);
    }
    return self::noConst;
  }

  static function _updateConstant($c, $k, $v, $formatValue = true) {
    foreach (self::$funcs as $func) {
      $c = preg_replace_callback(self::regexp($func, $k), function($m) use ($func, $k, $v, $formatValue) {
        return $func."('$k', ".($formatValue ? Arr::formatValue($v) : $v).");";
      }, $c);
    }
    return $c;
  }

  static $constantsRegexp = '(?:define|setConstant)\([\'"](.*)["\'],\s*(.*)\)\s*;';

  static $vars;

  static $funcs = ['define', 'setConstant'];

  static function updateConstant($file, $k, $v, $formatValue = true) {
    $c = self::_updateConstant(file_get_contents($file), $k, $v, $formatValue);
    file_put_contents($file, $c);
  }

  static function updateConstants($file, $constants, $formatValue = true) {
    $c = file_get_contents($file);
    foreach ($constants as $k => $v) {
      if (!isset($v)) throw new Exception("[$k] not defined");
      $c = self::_updateConstant($c, $k, $v, $formatValue);
    }
    file_put_contents($file, $c);
  }

  static function addConstant($file, $k, $v) {
    file_put_contents($file, self::_addConstant(file_get_contents($file), $k, $v));
  }

  static function _addConstant($c, $k, $v) {
    $c = trim($c);
    if (substr($c, strlen($c) - 2, 2) == '?>') {
      // убираем закрывающий PHP-тэг в конце
      $c = substr($c, 0, strlen($c) - 2);
      $c = trim($c);
    }
    return $c."\n\n"."if (!defined('$k')) define('$k', ".Arr::formatValue($v).");";
  }

  static function replaceConstant($file, $k, $v) {
    self::deleteConstant($file, $k);
    self::addConstant($file, $k, $v);
    if (function_exists('opcache_reset')) opcache_reset();
  }

  static function _replaceConstant($c, $k, $v) {
    $c = self::_deleteConstant($c, $k);
    $c = self::_addConstant($c, $k, $v);
    if (function_exists('opcache_reset')) opcache_reset();
    return $c;
  }

  static function replaceConstants($file, $constants) {
    $c = file_exists($file) ? file_get_contents($file) : "<?php\n";
    foreach ($constants as $k => $v) {
      $c = self::_deleteConstant($c, $k);
      $c .= "\n\n"."if (!defined('$k')) define('$k', ".Arr::formatValue($v).");";
    }
    file_put_contents($file, $c);
    if (function_exists('opcache_reset')) opcache_reset();
  }

  static function cleanupConstants($file) {
    $constants = self::getConstants($file);
    ksort($constants);
    $c = '';
    foreach ($constants as $k => $v) $c .= "\n\n"."if (!defined('$k')) define('$k', ".Arr::formatValue($v).");";
    file_put_contents($file, "<?php$c");
    if (function_exists('opcache_reset')) opcache_reset();
  }

  static function deleteConstant($file, $k) {
    file_put_contents($file, self::_deleteConstant(file_get_contents($file), $k));
  }

  static function _deleteConstant($c, $k) {
    $c = preg_replace(self::regexp('\s*if\s*\(\s*!defined\([\'"]'.$k.'[\'"]\s*\)\s*\)\s* define', $k), '', $c);
    $c = preg_replace(self::regexp('define', $k), '', $c);
    $c = preg_replace(self::regexp('setConstant', $k), '', $c);
    $c = preg_replace("/\n{2,}/", "\n\n", $c);
    return $c;
  }

  /**
   * Получает список всех констант, используемых в файле
   *
   * @param string $file Путь к файлу
   * @param bool $quietly
   * @return array|bool
   * @throws Exception
   */
  static function getConstants($file, $quietly = false) {
    if (!file_exists($file)) {
      if (!$quietly) throw new Exception("File '$file' does not exists");
      else return false;
    }
    require_once $file;
    $c = self::parseConstants(file_get_contents($file));
    if (!$c) return false;
    $r = [];
    foreach ($c as $k => $v) $r[$k] = getConstant($k);
    return $r;
  }

  static function parseConstants($s) {
    preg_match_all('/'.self::$constantsRegexp.'/i', $s, $m);
    if (!$m[1]) return false;
    $constants = [];
    for ($i = 0; $i < count($m[1]); $i++) {
      $constants[$m[1][$i]] = Arr::deformatValue($m[2][$i]);
    }
    return $constants;
  }

  static function getAllConstants($folder) {
    if ($folder[strlen($folder) - 1] == '/') $folder = substr($folder, 0, strlen($folder) - 1);
    $items = [];
    foreach (Dir::files($folder) as $entry) {
      $key = str_replace('.php', '', $entry);
      if (!$constants = self::getConstants($folder.'/'.$entry)) continue;
      $items[$key] = $constants;
    }
    return $items;
  }

  static function getAllConstantsFlat($folder) {
    $r = [];
    foreach (self::getAllConstants($folder) as $constants) {
      foreach ($constants as $name => $value) {
        $r[$name] = $value;
      }
    }
    return $r;
  }

  static function loadConstants($name) {
    if (($filePaths = self::getFilePaths($name, 'constants')) !== false) {
      foreach ($filePaths as $filePath) include_once $filePath;
    }
    else {
      throw new Exception('"config/constants/'.$name.'.php" not found');
    }
  }

  static function getVars($folder) {
    return self::_getVars($folder, true);
  }

  static function getVarConfigs($folder) {
    return self::_getVars($folder, false);
  }

  static function _getVars($folder, $vars = true) {
    if (!is_dir($folder)) return false;
    foreach (Dir::files($folder) as $file) {
      $key = str_replace('.php', '', $file);
      if ($vars) $items[$key] = self::getVar($key, true);
      else
        $items[] = $key;
    }
    return $items;
  }

  const noConst = 311111;

  /**
   * @param $file
   * @param $k
   * @param bool $quietly
   * @return bool|string
   * @throws Exception
   * @throws NoFileException
   */
  static function getConstant($file, $k, $quietly = false) {
    if (!file_exists($file)) {
      if ($quietly) return false;
      else throw new NoFileException($file);
    }
    if (($r = self::__getConstant(file_get_contents($file), $k)) !== self::noConst) return $r;
    if ($quietly) return false;
    else throw new Exception("There is no constant '$k' in file '$file'");
  }

  static function constantExists($file, $k) {
    if (!file_exists($file)) return false;
    return (self::__getConstant(file_get_contents($file), $k) != self::noConst);
  }

  static function _getConstant($c, $k, $quietly = false) {
    if (($r = self::__getConstant($c, $k)) != self::noConst) return $r;
    if ($quietly) return false;
    else throw new Exception("There is no constant '$k'");
  }

  static function updateVar($file, $v) {
    if (!strstr($file, '/vars/')) throw new Exception('Use FileVar class for that');
    FileVar::updateVar($file, $v);
    self::cleanCache($file);
  }

  static function updateSubVar($file, $k, $v) {
    if (!strstr($file, '/vars/')) throw new Exception('Use FileVar class for that');
    FileVar::updateSubVar($file, $k, $v);
    self::cleanCache($file);
  }

  static function removeSubVar($file, $k) {
    if (!strstr($file, '/vars/')) throw new Exception('Use FileVar class for that');
    FileVar::removeSubVar($file, $k);
    self::cleanCache($file);
  }

  static function cleanCache($file) {
    $key = str_replace('.php', '', explode('/vars/', $file)[1]);
    if (isset(self::$vars[$key])) unset(self::$vars[$key]);
  }

  /**
   * Возвращает массив с данными конфигурации
   */
  static function getVar($key, $quietly = false, $merged = true) {
    if (isset(self::$vars[$key])) return self::$vars[$key];
    if (($filePaths = self::getFilePaths($key, 'vars')) !== false) {
      $r = false;
      if ($merged) {
        foreach ($filePaths as $path) {
          if (is_array($r)) {
            $r = array_merge($r, include $path);
          }
          else $r = include $path;
        }
      } else {
        $r = include Arr::last($filePaths);
      }
    }
    else {
      if (!$quietly) throw new Exception("Var '$key' not found");
      return false;
    }
    self::$vars[$key] = $r;
    return $r;
  }

  /**
   * @param $k1
   * @param $k2
   * @param bool $quietly
   * @return bool|array
   * @throws Exception
   */
  static function getVarVar($k1, $k2, $quietly = false) {
    if (!($v = self::getVar($k1, $quietly))) return false;
    if (!isset($v[$k2])) {
      if (!$quietly) throw new Exception("var '$k2' not defined in config section '$k1'");
      else return false;
    }
    return $v[$k2];
  }

  static function getSubVar($key, $subKey, $strict = false, $quietly = false) {
    $v = self::getVar($key, $quietly);
    if ($strict and !isset($v[$subKey])) throw new Exception("SubKey '$subKey' of '$key' section not defined");
    return isset($v[$subKey]) ? $v[$subKey] : null;
  }

  static protected $_basePaths = [];
  static $basePaths = [];

  static function addBasePath($path, $priority = 0) {
    self::$_basePaths[] = [$path, $priority];
    self::$basePaths = Arr::get(Arr::sortByOrderKey(self::$_basePaths, 1, SORT_DESC), 0);
  }

  static function getFilePaths($path, $folder) {
    $path = $path.'.php';
    foreach (self::$basePaths as $basePath) {
      $p = "$basePath/$folder/$path";
      if (file_exists($p)) $r[] = $p;
    }
    if (empty($r)) return false;
    return array_reverse($r); // потому что там мердж дальше. и то, что имеет высший приоритет должно быть последним для перезапиисывания
  }

  static function getFileVar($file, $quietly = true) {
    if (file_exists($file)) return include ($file);
    else {
      if (!$quietly) throw new Exception("Path '$file' not found");
      return false;
    }
  }

  static function var2declaration($var, $depth = 1) {
    if (is_array($var)) {
      foreach ($var as $k => $v) {
        $_[] = str_repeat(self::$tab, $depth).(is_numeric($k) ? $k : "'$k'")." => ".self::var2declaration($v, $depth + 1);
      }
      return "array(\n".implode(",\n", $_)."\n".str_repeat(self::$tab, $depth - 1).");";
    }
    else {
      return Arr::formatValue($var);
    }
  }

  //-------------------------------------------------------------------------------

  /**
   * Возвращает массив с существующими структурами конфигурационных констант или переменных
   *
   * @param string $folder Путь до каталога "ngn" или "site"
   * @param string $type "constants" / "vars"
   * @return array|mixed
   */
  static function getStruct($folder, $type) {
    if (!file_exists($folder.'/config/struct/'.$type.'.php')) return [];
    return include $folder.'/config/struct/'.$type.'.php';
  }

  // --------------------------------------------------------------------------------

  static function createConstants($file, $constants) {
    file_put_contents($file, self::createConstantsStr($constants));
  }

  static function createConstantsStr($constants) {
    $c = "<?php\n";
    foreach ($constants as $k => $v) {
      $c .= "\n\n"."if (!defined('$k')) define('$k', ".Arr::formatValue($v).");";
    }
    return $c;
  }

}