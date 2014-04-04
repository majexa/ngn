<?php

class RouterScripts extends RouterCommon {

  /**
   * Тип контроллера: scripts/controllers
   *
   * @var string
   */
  private $controllerType;

  protected function getFrontendName() {
    return $this->req->params[0] == 'default' ? 'default' : false;
  }

  protected function init() {
    $this->controllerType = $this->req->params[0][0] == 's' ? 'scripts' : 'controllers';
    if ($this->req->params[0] == 's2' or $this->req->params[0] == 'c2') {
      $this->isDb = false;
    }
    else {
      $this->isDb = true;
    }
    parent::init();
  }

  protected function getCommonControllerParamN() {
    return 1;
  }

  function _getController() {
    if ($this->controllerType == 'scripts') {
      // Для JavaScript'ов и CSS:
      // - включить PLAIN TEXT режим
      // - выключить нотисы
      $staticFilesMode = (isset($this->req->params[1]) and
        ($this->req->params[1] == 'js' or $this->req->params[1] == 'css')) ? true : false;
      if ($staticFilesMode) R::set('plainText', true);
      $controller = new CtrlScripts($this);
      if ($staticFilesMode) Err::noticeSwitch(true);
      return $controller;
    }
    else {
      return parent::_getController();
    }
  }

  static function prefixes() {
    return ['s', 's2', 'c', 'c2', 'default'];
  }

}