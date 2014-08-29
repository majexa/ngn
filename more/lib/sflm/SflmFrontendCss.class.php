<?php

class SflmFrontendCss extends SflmFrontend {

  protected function __addPath($path, $source = null) {
    $this->_addPath($path);
  }

  function addPath($path) {
    $this->_addPath($path);
  }

  function addStaticPath($_path) {
    foreach (Sflm::$absBasePaths as $k => $path) {
      if (file_exists("$path/{$this->base->type}/$_path.{$this->base->type}")) {
        $this->addPath("$k/{$this->base->type}/$_path.{$this->base->type}");
        return;
      }
    }
  }

}
