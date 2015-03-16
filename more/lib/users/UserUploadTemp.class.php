<?php

class UserUploadTemp {

  static function moveFromRequest(Req $req) {
    $id = Auth::get('id') ?: session_id();
    $f = "/temp/$id/";
    Dir::make(UPLOAD_PATH.$f);
    $image = new Image;
    $r = [];
    foreach ($req->files['images'] as $file) {
      $name = rand(100000, 999999);
      copy($file['tmp_name'], UPLOAD_PATH.$f.$name.'.png');
      $image->resizeAndSave(UPLOAD_PATH.$f.$name.'.png', UPLOAD_PATH.$f.'sm_'.$name.'.png', 100, 100);
      $r[] = '/'.UPLOAD_DIR.$f.$name.'.png';
    }
    return $r;
  }

  static function get($auth = false) {
    if ($auth) {
      $id = Auth::get('id');
    }
    else {
      $id = Auth::get('id') ?: session_id();
    }
    if ($id === null) return [];
    $r = [];
    $image = new Image;
    foreach (glob(UPLOAD_PATH.'/temp/'.$id.'/*') as $file) {
      if (Misc::hasPrefix('sm_', basename($file))) continue;
      $v = str_replace(UPLOAD_PATH, UPLOAD_DIR, $file);
      $image->resizeAndSave($file, Misc::getFilePrefexedPath($file, 'sm_'), 100, 100);
      $r[] = $v;
    }
    return $r;
    //die2($r);
//    return array_map(function ($v) {
//      return
//    }, array_filter(, function ($v) {
//      return !Misc::hasPrefix('sm_', basename($v));
//    }));
  }

  static function moveSessionToAuth($userId) {
    $id = session_id();
    $from = UPLOAD_PATH.'/temp/'.$id;
    $to = UPLOAD_PATH.'/temp/'.$userId;
    if (!file_exists($from)) return;
    Dir::remove($to);
    rename($from, $to);
  }

  /**
   * Удаляет папки старше часа
   */
  static function cleanup() {
    array_map(function ($folder) {
      output("remove $folder");
      Dir::remove($folder);
    }, array_filter(glob(UPLOAD_PATH.'/temp/*'), function ($folder) {
      return filectime($folder) < time();
    }));
  }

}