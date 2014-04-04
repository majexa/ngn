<?php

class FileList {
  
  static function get($file) {
    if (!file_exists($file)) return [];
    return explode("\n", trim(file_get_contents($file)));
  }
  
  static function replace($file, array $data) {
    file_put_contents($file, implode("\n", $data));
  }
  
  static function merge($file, array $data) {
    file_put_contents($file, implode("\n", array_merge(self::get($file), $data)));
  }
  
  static function addItem($file, $item) {
    self::merge($file, [$item]);
  }
  
}
