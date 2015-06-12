<?php

class SflmCache extends FileCache {

  static function c(array $options = []) {
    $options['lifetime'] = null;
    return parent::c($options);
  }

  static function clean() {
    foreach (array_filter(glob(static::folder().'/*'), function($file) {
      return Misc::hasPrefix('zend_cache---sflm', basename($file));
    }) as $file) {
      unlink($file);
    }
  }

}