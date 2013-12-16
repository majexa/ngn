<?php

Lang::load('admin');
set_time_limit(70);

class AdminRouter extends Router {

  /**
   * Текущий модуль администрирования
   *
   * @var strgin
   */
  protected $module;

  /**
   * Текущий каталог модуля администрирования
   *
   * @var strgin
   */
  protected $moduleSubfolder;

  protected $allowedAdminModules;

  function getFrontend() {
    return 'admin';
  }

  function _getController() {
    if (empty($this->req->params[0])) {
      redirect('admin');
      return;
    }
    if (Auth::get('id') and isset($this->req->params[1])) {
      $this->module = $this->req->params[1];
      $this->moduleSubfolder = '/'.$this->module;
    }
    else {
      $this->module = 'default';
      $this->moduleSubfolder = '';
    }
    $this->allowedAdminModules = AdminModule::getAllowedModules();
    Sflm::$frontend = 'admin';
    return $this->__getController();
  }

  protected function __getController() {
    if ($this->req->params[0] == 'god' and Auth::get('id') and !Misc::isGod()) {
      throw new Exception("God mode not allowed:\n"."Possible reasons:\n"."* Current user is not god\n"."* Current IP is not presents in developers IPs list\n");
    }
    //if (!AdminModule::isAllowed($this->module)) throw new Exception("Admin module '{$this->module}' not allowed");
    $class = ClassCore::nameToClass('CtrlAdmin', $this->module);
    if (Lib::exists($class)) {
      return new $class($this);
    }
    else {
      throw new NotLoggableError("Module '{$this->module}' not found. class '$class'");
    }
  }

  protected function auth() {
    Auth::$errorsText = [
      Auth::ERROR_AUTH_NO_LOGIN        => LANG_AUTH_NO_LOGIN,
      Auth::ERROR_AUTH_USER_NOT_ACTIVE => LANG_AUTH_USER_NOT_ACTIVE,
      Auth::ERROR_AUTH_WRONG_PASS      => LANG_AUTH_WRONG_PASS
    ];
    parent::auth();
  }

}