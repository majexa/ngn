<?php

class SflmCss extends SflmBase {

  public $type = 'css';

  function getTag($path) {
    return '<link rel="stylesheet" type="text/css" href="'.$path.'" media="screen, projection" />'."\n";
  }

  protected function getContents($path) {
    if (Misc::hasSuffix('.scss', $path)) {
      return O::get('Scssc')->compile(file_get_contents($path));
    }
    return file_get_contents($path);
  }

}
