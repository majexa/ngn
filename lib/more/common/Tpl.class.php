<?php


// получить список главных шаблонов

class Tpl {
  
  static $master;

  static function getSettingsFields($path) {
    preg_match(
      '/@tplSettings:(.*)\*\/\?>(.*)/U',
      str_replace("\n", '', file_get_contents(Tt()->exists($path))),
      $m
    );
    return eval('return '.$m[1].';');
  }
  
  static function saveSettings($path, $settings) {
    Settings::set('tplSettings.'.self::clearSlashes($path), $settings);
  }
  
  static function getSettings($path) {
    return Settings::get('tplSettings.'.self::clearSlashes($path));
  }
  
  static function clearSlashes($path) {
    return str_replace('/', '~', $path);
  }
  
  static function returnSlashes($path) {
    return str_replace('~', '/', $path);    
  }
  
  static function getList($masterFolder, $parentPath = null) {
    $list = [];
    $tplFolder = $masterFolder.'/'.$parentPath;
    if (!($dir = dir($tplFolder))) return $list;
    while (false !== $entry = $dir->read()) {
      if ($entry[0] == '.') continue;
      if (is_dir($tplFolder.'/'.$entry)) {
        $list = Arr::append($list, self::getList(
          $masterFolder,
          $parentPath ? ($parentPath.'/'.$entry) : $entry
        ));
      } elseif (preg_match('/(.*).php/', $entry, $m)) {
        $list[] = ($parentPath ? $parentPath.'/' : '').$m[1];
      }
    }
    return $list;
  }
  
  static function getListNGN() {
    return self::getList(NGN_PATH.'/tpl');
  }

  static function getListMaster() {
    return self::getList(MASTER_PATH.'/tpl');
  }
  
  static function getListSite() {
    return self::getList(SITE_PATH.'/tpl');
  }
  
  static function setMaster($master) {
    self::$master = $master;
  }
  
  static function setSlave($master) {
    self::$master = $master;
  }
  
}
