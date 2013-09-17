<?php

class SflmJs extends SflmBase {

  public $type = 'js', $paths, $frontend;

  function getTag($path) {
    return '<script src="'.$path.'" type="text/javascript"></script>'."\n";
  }

  protected function getPackageCodeR($package) {
    Err::noticeSwitch(false);
    $code = parent::getPackageCodeR($package);
    Err::noticeSwitchBefore();
    return $code;
  }

}