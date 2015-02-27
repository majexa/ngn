<?php

class SflmFrontendCss extends SflmFrontend {

  protected function __addPath($path, $source = null) {
    $this->_addPath($path);
  }

  function addPath($path) {
    $this->_addPath($path);
  }

  function _addPath($path) {
    $file = $this->base->getAbsPath($path);
    if (file_exists($file) and preg_match_all('/@import (.*)/', file_get_contents($file), $m)) {
      foreach ($m[1] as $v) parent::_addPath(trim($v, '"\'; '));
    }
    parent::_addPath($path);
  }

  function addStaticPath($_path) {
    foreach (Sflm::$absBasePaths as $k => $path) {
      if (file_exists("$path/{$this->base->type}/$_path.{$this->base->type}")) {
        $this->addPath("$k/{$this->base->type}/$_path.{$this->base->type}");
        return;
      }
    }
  }

  protected $absFiles = [];

  function addFolder($absFolder) {
    foreach (glob("$absFolder/*.css") as $file) {
      $this->absFiles[] = $file;
    }
  }

  function _code() {
    $code = preg_replace('/@import (.*)/', '', parent::_code());
    foreach ($this->absFiles as $file) $code .= "\n/*--|$file|--*/\n".file_get_contents($file);
    return $code;
  }

}
