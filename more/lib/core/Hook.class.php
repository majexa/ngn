<?php

class Hook {

  static function paths($path, $pageModule = null) {
    foreach (Ngn::$basePaths as $basePath) {
      $p = "$basePath/hooks/$path.php";
      if (file_exists($p)) $paths[] = $p;
    }
    if ($pageModule) {
      if (($info = PageModuleCore::getInfo($pageModule)) !== false and
        (($file = $info->getFile('hooks/'.$path)) !== false)
      ) {
        $paths[] = $file;
      }
    }
    return isset($paths) ? $paths : false;
  }

}