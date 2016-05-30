<?php

class Locale {

  static function get($name) {
    $lang = Config::getVar('locale/'.LOCALE.'/core');
    if (isset($lang[$name])) {
      return $lang[$name];
    } else {
      return Misc::nameToTitle($name);
    }
  }

}
