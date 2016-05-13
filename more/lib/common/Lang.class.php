<?php

if (!defined('LANG')) define('LANG', 'ru');

class Lang {

  static function get($name) {
    $lang = Config::getVar('lang/'.LANG);
    if (isset($lang[$name])) {
      return $lang[$name];
    } else {
      return Misc::nameToTitle($name);
    }
  }

}
