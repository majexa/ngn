<?php

class SiteCore {

  static function clearTemp() {
    Dir::clear(TEMP_PATH);
  }

}
