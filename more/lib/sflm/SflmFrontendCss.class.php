<?php

class SflmFrontendCss extends SflmFrontend {

  protected function __addPath($path, $source = null) {
    $this->_addPath($path);
  }

  function addPath($path) {
    $this->_addPath($path);
  }

}
