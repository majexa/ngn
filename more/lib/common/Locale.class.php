<?php

class Locale {

  static function get($name, $section = 'core') {
    $lang = Config::getVar('locale/'.LOCALE.'/'.$section, true) ?: [];
    if (isset($lang[$name])) {
      return $lang[$name];
    } else {
      return Misc::nameToTitle($name);
    }
  }

}
