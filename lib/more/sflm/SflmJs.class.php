<?php

class SflmJs extends SflmBase {

  public $type = 'js', $paths, $frontend;

  function getTag($path) {
    return '<script src="'.$path.'?'.$this->version.'" type="text/javascript"></script>'."\n";
  }

  protected function getPackageCodeR($package) {
    Err::noticeSwitch(false);  // Выключаем отображение нотисов
    $code = parent::getPackageCodeR($package);
    Err::noticeSwitchBefore(); // Включаем
    return $code;
  }

}
