<?php

class DefaultRouter extends Router {

  function getFrontend() {
    return 'default';
  }

  protected function prefix() {
    return 'Ctrl';
  }

  /**
   * Какие у нас бывают роутинги
   * Ctrl[ProjectName]Default
   * CtrlDefault
   * Ctrl[param 0]
   *
   * @return bool
   */
  function getController() {
    if (isset($this->req->params[0])) {
      $class = $this->prefix().ucfirst($this->req->params[0]);
    }
    if (isset($class) and class_exists($class)) {}
    else {
      if (class_exists('Ctrl'.ucfirst(PROJECT_KEY).'Default')) $class = 'Ctrl'.ucfirst(PROJECT_KEY).'Default';
      elseif (class_exists($this->prefix().'Default')) $class = $this->prefix().'Default';
      else $class = 'CtrlDefault';
      if (!class_exists($class)) return false; // throw new NotFoundException("ctrl $class");
    }
    if ($this->req['showRouter']) die2("Current controller is: <u>$class</u>");//"\nPath: ".Lib::getPath($class));
    return new $class($this);
  }

}