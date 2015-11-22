<?php

class DefaultRouter extends Router {

  protected function getFrontendName() {
    return 'default';
  }

  protected function prefix($prefix = '') {
    return 'Ctrl'.$prefix;
  }

  protected function getControllerClass($prefix = '') {
    $ctrl = 'Ctrl'.$prefix;
    if (isset($this->req->params[0])) {
      $class = $ctrl.ucfirst(PROJECT_KEY).ucfirst($this->req->params[0]);
      if (!class_exists($class)) {
        $class = $this->prefix($prefix).$prefix.ucfirst($this->req->params[0]);
      }
    }
    if (isset($_SERVER['HTTP_HOST'])) {
      $subDomain = rtrim(Misc::removeSuffix(SITE_DOMAIN, $_SERVER['HTTP_HOST']), '.');
    } else {
      $subDomain = false;
    }
    if (!isset($class) or !class_exists($class)) {
      if ($subDomain and class_exists($ctrl.ucfirst(PROJECT_KEY).ucfirst($subDomain))) {
        $class = $ctrl.ucfirst(PROJECT_KEY).ucfirst($subDomain);
      }
      elseif (class_exists($ctrl.ucfirst(PROJECT_KEY).'Default')) {
        $class = $ctrl.ucfirst(PROJECT_KEY).'Default';
      }
      elseif (class_exists($this->prefix($prefix).'Default')) {
        $class = $this->prefix($prefix).'Default';
      }
      else {
        $class = $ctrl.'Default';
      }
      if (!class_exists($class)) return false; // throw new NotFoundException("ctrl $class");
    }
    return $class;
  }

  /**
   * Какие у нас бывают роутинги
   * Ctrl[ProjectName]Default
   * CtrlDefault
   * Ctrl[param 0]
   *
   * @return bool|Router
   */
  function _getController() {
    if (!($class = $this->getControllerClass())) throw new Exception('no ctrl');
    return new $class($this);
  }

}