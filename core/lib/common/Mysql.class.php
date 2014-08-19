<?php

class Mysql {

  static $mysqlPath = 'mysql';
  static $mysqlAdminPath = 'mysqladmin';
  static $mysqlDumpPath = 'mysqldump';

  static function renameDb($rootUser, $rootPass, $rootHost, $from, $to) {
    if ($from == $to) throw new Exception('Can\'t replace DB "'.$to.'" with itself');
    $uph = " -u$rootUser -p$rootPass -h$rootHost";
    sys(self::$mysqlAdminPath.$uph.' create '.$to);
    sys(self::$mysqlDumpPath.$uph.' --default-character-set=utf8 '.$from.' | '.self::$mysqlPath.$uph.' '.$to.'');
    sys(self::$mysqlAdminPath.$uph.' -f drop '.$from);
  }

  static function copyDb($rootUser, $rootPass, $rootHost, $from, $to) {
    if ($from == $to) throw new Exception('Can\'t replace DB "'.$to.'" with itself');
    $uph = " -u$rootUser -p$rootPass -h$rootHost";
    sys(self::$mysqlAdminPath.$uph.' create '.$to);
    sys(self::$mysqlDumpPath.$uph.' --default-character-set=utf8 '.$from.' | '.self::$mysqlPath.$uph.' '.$to.'');
  }

  static function dump($rootUser, $rootPass, $rootHost, $dbName, $file) {
    $uph = " -u$rootUser -p$rootPass -h$rootHost";
    sys(self::$mysqlDumpPath.$uph." -f $dbName > $file");
  }

}
