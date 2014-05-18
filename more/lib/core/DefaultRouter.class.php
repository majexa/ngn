<?php

class DefaultRouter extends Router {

  protected function getFrontendName() {
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
  function _getController() {
    if (isset($this->req->params[0])) {
      $class = 'Ctrl'.ucfirst(PROJECT_KEY).ucfirst($this->req->params[0]);
      if (!class_exists($class)) {
        $class = $this->prefix().ucfirst($this->req->params[0]);
      }
    }
    if (!isset($class) or !class_exists($class)) {
      if (class_exists('Ctrl'.ucfirst(PROJECT_KEY).'Default')) $class = 'Ctrl'.ucfirst(PROJECT_KEY).'Default';
      elseif (class_exists($this->prefix().'Default')) $class = $this->prefix().'Default';
      else $class = 'CtrlDefault';
      if (!class_exists($class)) return false; // throw new NotFoundException("ctrl $class");
    }
    return new $class($this);
  }

}