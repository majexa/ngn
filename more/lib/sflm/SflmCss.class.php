<?php

class SflmCss extends SflmBase {

  public $type = 'css';

  function getTag($path) {
    return '<link rel="stylesheet" type="text/css" href="'.$path.'" media="screen, projection" />'."\n";
  }

}
