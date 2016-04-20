<?php

if (!defined('LANG')) define('LANG', 'ru');

class Lang {

  static function get($name) {
    if (strstr($name, ' ')) {
      return Config::getVar('lang/'.LANG)[$name];
    } else {
      return Misc::nameToTitle($name);
    }
  }

}
