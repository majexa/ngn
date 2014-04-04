<?php

class FileVar {

  static function formatVar($v) {
    return "<?php\n\nreturn ".Arr::formatValue(Arr::transformValue($v)).";\n";
  }

  static function updateVar($file, $v) {
    Dir::make(dirname($file));
    file_put_contents($file, self::formatVar($v));
  }

  static function updateSubVar($file, $k, $v) {
    $r = file_exists($file) ? include $file : [];
    $r[$k] = $v;
    self::updateVar($file, $r);
  }

  static function removeSubVar($file, $k) {
    $r = include $file;
    unset($r[$k]);
    self::updateVar($file, $r);
  }

  static function getVar($file) {
    return require $file;
  }

  static function touch($file) {
    if (!file_exists($file)) self::updateVar($file, []);
  }

}