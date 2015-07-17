<?php

class ProjectConfig {

  static $varsFolder;

  static function updateConstant($name, $k, $v, $formatValue = true) {
    Config::updateConstant(PROJECT_PATH.'/config/constants/'.$name.'.php', $k, $v, $formatValue);
  }

  static function updateConstants($name, $constants, $formatValue = true) {
    Config::updateConstants(PROJECT_PATH.'/config/constants/'.$name.'.php', $constants, $formatValue);
  }

  static function addConstant($name, $k, $v) {
    Config::addConstant(PROJECT_PATH.'/config/constants/'.$name.'.php', $k, $v);
  }

  static function addConstants($name, $constants) {
    foreach ($constants as $k => $v) Config::addConstant(PROJECT_PATH.'/config/constants/'.$name.'.php', $k, $v);
  }

  static function deleteConstant($name, $k) {
    Config::deleteConstant(PROJECT_PATH.'/config/constants/'.$name.'.php', $k);
  }

  static function replaceConstant($name, $k, $v) {
    Config::replaceConstant(PROJECT_PATH.'/config/constants/'.$name.'.php', $k, $v);
  }

  static function replaceConstants($name, $constants) {
    foreach ($constants as $k => $v) {
      if (is_numeric($v)) $v = (int)$v;
      if (getConstant($k) !== $v) $new[$k] = $v;
    }
    if (isset($new)) Config::replaceConstants(PROJECT_PATH.'/config/constants/'.$name.'.php', $new);
  }

  static function createConstants($name, $constants) {
    Config::createConstants(PROJECT_PATH.'/config/constants/'.$name.'.php', $constants);
  }

  static function cleanupConstants($name) {
    Config::cleanupConstants(PROJECT_PATH.'/config/constants/'.$name.'.php');
  }

  static function getConstants($name, $quietly = false) {
    $r = [];
    foreach (Ngn::$basePaths as $basePath) {
      if (($v = Config::getConstants($basePath.'/config/constants/'.$name.'.php', $quietly)) !== false) {
        $r = array_merge($r, $v);
      }
    }
    return $r;
  }

  static function getConstant($name, $k, $quietly = false) {
    return Config::getConstant(PROJECT_PATH.'/config/constants/'.$name.'.php', $k, $quietly);
  }

  static function getAllConstants() {
    return Config::getAllConstants(PROJECT_PATH.'/config/constants');
  }

  static function updateVar($k, $v, $ignoreExistence = false) {
    if (($vars = Config::getFilePaths($k, 'vars')) !== false) {
      $defaultValue = Config::getFileVar($vars[0]);
    } else {
      $defaultValue = [];
    }
    if (is_array($v) and !$ignoreExistence) {
      foreach ($v as $key => $value)
        if (!isset($defaultValue[$key]) or $defaultValue[$key] != $value) $newValue[$key] = $value;
    } else {
      if (serialize($defaultValue) != serialize($v)) $newValue = $v;
    }
    if (isset($newValue)) {
        unset(Config::$vars[$k]);
      Config::updateVar(self::$varsFolder."/$k.php", $newValue);
    } else {
      //SiteConfig::deleteVarSection($k);
    }
  }

  static function getVarConfigs() {
    return Config::getVarConfigs(self::$varsFolder);
  }

  static function getVars() {
    if (Misc::isGod()) return Config::getVars(self::$varsFolder);
    if (!$allowed = self::getVar('allowedConfigVars', true)) return false;
    foreach (Config::getVars(self::$varsFolder) as $k => $v) {
      if (in_array($k, $allowed)) $r[$k] = $v;
    }
    return $r;
  }

  static function updateSubVar($name, $subKey, $value) {
    Config::updateSubVar(self::$varsFolder."/$name.php", $subKey, $value);
  }

  static function getConfigFiles() {
    if (Misc::isGod()) return Config::getVars(self::$varsFolder);
    if (!$allowed = self::getVar('allowedConfigVars', true)) return false;
    foreach (Config::getVars(self::$varsFolder) as $k => $v) {
      if (in_array($k, $allowed)) $r[$k] = $v;
    }
    return $r;
  }

  static function getNames($type) {
    return Config::_getVars(PROJECT_PATH.'/config/'.$type, false);
  }

  static function getTitles($type) {
    $structures = self::getStruct($type);
    foreach ($structures as $name => $v) {
      $r[$name] = isset($v['title']) ? $v['title'] : $name;
    }
    return $r;
  }

  /**
   * Возвращает массив с существующими структурами конфигурационных констант или переменных
   *
   * @param   string  "constants" / "vars"
   * @return  array
   */
  static function getStruct($type) {
    $r = [];
    foreach (array_reverse(Ngn::$basePaths) as $path) {
      $r += Config::getStruct($path, $type);
    }
    return $r;
  }

  static function deleteVarSection($name) {
    File::delete(self::$varsFolder.'/'.$name.'.php');
  }

  static function hasSiteVar($name) {
    $file = self::$varsFolder.'/'.$name.'.php';
    return file_exists($file) ? $file : false;
  }

  static function renameVar($prefix, $from, $to) {
    foreach (glob(self::$varsFolder.'/'.$prefix.'*') as $file) {
      rename($file, str_replace($from, $to, $file));
    }
  }

  static function getVarsByPrefix($prefix) {
    $r = [];
    foreach (glob(self::$varsFolder.'/'.$prefix.'*') as $file) {
      $k = str_replace(self::$varsFolder, '', $file);
      $v = include $file;
      $r[$k] = $v;
    }
    return $r;
  }

}

ProjectConfig::$varsFolder = PROJECT_PATH.'/config/vars';
