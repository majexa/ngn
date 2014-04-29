<?php

class SflmCache extends FileCache {

  static function c(array $options = []) {
    $options['lifetime'] = null;
    return parent::c($options);
  }

}