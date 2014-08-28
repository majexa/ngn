<?php

class Hook {

  static function paths($path) {
    $paths = [];
    foreach (Ngn::$basePaths as $basePath) {
      $p = "$basePath/hooks/$path.php";
      if (file_exists($p)) $paths[] = $p;
    }
    return $paths;
  }

}