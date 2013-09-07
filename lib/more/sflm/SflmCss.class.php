<?php

class SflmCss extends SflmBase {

  public $type = 'css';

  function getTag($path) {
    if ($path == 'default') die2(22);
    return '<link rel="stylesheet" type="text/css" href="'.$path.'?'.$this->version.'" media="screen, projection" />'."\n";
  }

}
