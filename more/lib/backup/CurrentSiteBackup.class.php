<?php

if (!defined('NGN_ENV_PATH')) define('NGN_ENV_PATH', dirname(NGN_PATH));

if (!file_exists(NGN_ENV_PATH)) throw new NoFileException(NGN_ENV_PATH);

class CurrentSiteBackup {

  static $path;
  static $tempPath;
  static $maxBackups;
  static $dir;

  static function init() {
    self::$path = NGN_ENV_PATH.'/backup/'.SITE_DOMAIN;
    self::$tempPath = NGN_ENV_PATH.'/temp/backupRestore/'.SITE_DOMAIN;
    self::$maxBackups = 3;
  }

  static function make() {
    $dirs = Dir::dirs(self::$path);
    $lastId = Arr::last($dirs);
    if ($lastId >= self::$maxBackups) Dir::remove(self::$path.'/'.$dirs[0]);
    $backupDir = self::$path.'/'.($lastId + 1);
    Dir::make($backupDir);
    Zip::dir($backupDir.'/files.zip', WEBROOT_PATH);
    (new DbDumper(db()))->createDump($backupDir.'/db.sql');
    Zip::file($backupDir.'/db.zip', $backupDir.'/db.sql');
    unlink($backupDir.'/db.sql');
  }

  static function getList() {
    $r = [];
    foreach (Dir::dirs(self::$path) as $v) {
      $r[] = [
        'id'   => $v,
        'time' => filemtime(self::$path.'/'.$v)
      ];
    }
    return $r;
  }

  static function restore($id) {
    $backupDir = self::$path.'/'.$id;
    if (!file_exists($backupDir)) throw new Exception('Backup folder "'.self::$path.'/'.$id.'" does not exists');
    Dir::make(self::$tempPath);
    Zip::extract($backupDir.'/files.zip', self::$tempPath);
    Zip::extract($backupDir.'/db.zip', self::$tempPath);
    $dirs = Dir::dirs(self::$tempPath);
    Dir::moveContents(self::$tempPath.'/'.$dirs[0], WEBROOT_PATH);
    db()->delete();
    db()->importFile(self::$tempPath.'/db.sql');
    Dir::clear(self::$tempPath);
  }

  static function delete($id) {
    Dir::remove(self::$path.'/'.$id);
  }

}

CurrentSiteBackup::init();

