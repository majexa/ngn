<?php

if (!defined('LANG')) define('LANG', 'ru');

class Lang {

  static function get($name) {
    return Config::getVar('lang/'.LANG)[$name];
  }

}
