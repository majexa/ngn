<?php

class RouterCommon extends Router {

  protected function getCommonControllerParamN() {
    return 0;
  }

  protected function need2patch() {
  }

  function _getController() {
    $class = ClassCore::nameToClass('CtrlCommon', $this->req->param($this->getCommonControllerParamN()));
    if (!class_exists($class)) Err::error("Controller ($class) not found by path: ".Tt()->getPath());
    return new $class($this);
  }

}