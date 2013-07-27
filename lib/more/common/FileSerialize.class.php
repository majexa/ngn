<?php

class FileSerialize {
  
  static function get($file) {
    if (!file_exists($file)) return [];
    return unserialize(file_get_contents($file));
  }
  
  static function replace($file, array $data) {
    file_put_contents($file, serialize($data));
  }
  
  static function merge($file, array $data) {
    file_put_contents($file, serialize(array_merge(self::get($file), $data)));
  }
  
  static function addItem($file, $item) {
    self::merge($file, [$item]);
  }
  
}
