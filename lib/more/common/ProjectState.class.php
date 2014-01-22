<?php

class ProjectState {

  static protected function file($key) {
    return DATA_PATH.'/state/'.$key.'.php';
  }

  static function get($key, $quietly = false) {
    $file = static::file($key);
    $exists = file_exists($file);
    if (!$quietly and !$exists) throw new NoFileException($file);
    return $exists ? require $file : false;
  }

  static function update($key, $data) {
    FileVar::updateVar(static::file($key), $data);
  }

  static function updateSub($key, $subKey, $data) {
    FileVar::updateSubVar(static::file($key), $subKey, $data);
  }

}