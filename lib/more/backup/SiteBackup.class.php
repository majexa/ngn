<?php

class SiteBackup {

  static function getBackups($domain) {
    $dirs =  glob(NGN_ENV_PATH."/backup/$domain/*");
    $r = [];
    foreach ($dirs as $v)
      $r[] = [
        'id' => basename($v),
        'time' => filectime($v)
      ];
    return $r;
  }

}