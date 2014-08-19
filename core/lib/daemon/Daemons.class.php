<?php

class Daemons extends ArrayAccesseble {

  function __construct() {
    foreach (glob('/etc/init.d/*') as $file) {
      if (strstr(file_get_contents($file), '# ngn auto-generated worker')) {
        $this->r[] = $file;
      }
    }
  }

  function working() {
    //NGN_ENV_PATH
    //``
  }

}