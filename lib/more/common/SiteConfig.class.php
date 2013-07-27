<?php

class SiteConfig {

  static $varsFolder;

  static function updateConstant($name, $k, $v, $formatValue = true) {
    Config::updateConstant(SITE_PATH.'/config/constants/'.$name.'.php', $k, $v, $formatValue);
  }

  static function updateConstants($name, $constants, $formatValue = true) {
    Config::updateConstants(SITE_PATH.'/config/constants/'.$name.'.php', $constants, $formatValue);
  }

  static function addConstant($name, $k, $v) {
    Config::addConstant(SITE_PATH.'/config/constants/'.$name.'.php', $k, $v);
  }

  static function addConstants($name, $constants) {
    foreach ($constants as $k => $v) Config::addConstant(SITE_PATH.'/config/constants/'.$name.'.php', $k, $v);
  }

  static function deleteConstant($name, $k) {
    Config::deleteConstant(SITE_PATH.'/config/constants/'.$name.'.php', $k);
  }

  static function replaceConstant($name, $k, $v) {
    Config::replaceConstant(SITE_PATH.'/config/constants/'.$name.'.php', $k, $v);
  }

  static function replaceConstants($name, $constants) {
    foreach ($constants as $k => $v) {
      if (is_numeric($v)) $v = (int)$v;
      if (getConstant($k) !== $v) $new[$k] = $v;
    }
    if (isset($new)) Config::replaceConstants(SITE_PATH.'/config/constants/'.$name.'.php', $new);
  }

  static function createConstants($name, $constants) {
    Config::createConstants(SITE_PATH.'/config/constants/'.$name.'.php', $constants);
  }

  static function cleanupConstants($name) {
    Config::cleanupConstants(SITE_PATH.'/config/constants/'.$name.'.php');
  }

  static function getConstants($name, $quietly = false) {
    $c1 = Config::getConstants(NGN_PATH.'/config/constants/'.$name.'.php', $quietly);
    $c2 = Config::getConstants(SITE_PATH.'/config/constants/'.$name.'.php', $quietly);
    if ($c1 and $c2) return array_merge($c1, $c2);
    else return $c1 ?: $c2;
  }

  static function getNgnConstants($name) {
    return Config::getConstants(NGN_PATH.'/config/constants/'.$name.'.php');
  }

  static function getConstant($name, $k) {
    return Config::getConstant(SITE_PATH.'/config/constants/'.$name.'.php', $k);
  }

  static function getNgnConstant($name, $k) {
    return Config::getConstant(NGN_PATH.'/config/constants/'.$name.'.php', $k);
  }

  static function getAllConstants() {
    return Config::getAllConstants(SITE_PATH.'/config/constants');
  }

  static function updateVar($k, $v, $ignoreExistence = false) {
    $defaultValue = Config::getFileVar(Config::getFilePaths($k, 'vars')[0]);
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
    return Config::_getVars(SITE_PATH.'/config/'.$type, false);
  }

  static function getTitles($type) {
    $structs = self::getStruct($type);
    foreach ($structs as $name => $struct) {
      $r[$name] = isset($struct['title']) ? $struct['title'] : $name;
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
    $struct = Config::getStruct(NGN_PATH, $type);
    $struct += Config::getStruct(SITE_PATH, $type);
    return $struct;
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

SiteConfig::$varsFolder = SITE_PATH.'/config/vars';
