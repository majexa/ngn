<?php

set_time_limit(70);

class AdminRouter extends Router {

  /**
   * Текущий модуль администрирования
   *
   * @var string
   */
  protected $module;

  /**
   * Текущий каталог модуля администрирования
   *
   * @var string
   */
  protected $moduleSubfolder;

  protected $allowedAdminModules;

  protected function getFrontendName() {
    return 'admin';
  }

  function _getController() {
    if (empty($this->req->params[0])) {
      redirect('admin');
      die();
    }
    if (isset($this->req->params[1])) {
      $this->module = $this->req->params[1];
      $this->moduleSubfolder = '/'.$this->module;
    }
    else {
      $this->module = 'default';
      $this->moduleSubfolder = '';
    }
    $this->allowedAdminModules = AdminModule::getAllowedModules();
    return $this->__getController();
  }

  /**
   * @throws Exception
   * @throws NotLoggableError
   * @return CtrlBase
   */
  protected function __getController() {
    if ($this->req->params[0] == 'god' and Auth::get('id') and !Misc::isGod()) {
      throw new Exception("God mode not allowed:\n"."Possible reasons:\n"."* Current user is not god\n"."* Current IP is not presents in developers IPs list\n");
    }
    $adminClass = ClassCore::nameToClass('CtrlAdmin', $this->module);
    $commonClass = ClassCore::nameToClass('CtrlCommon', $this->module);
    if (class_exists($adminClass)) {
      return new $adminClass($this);
    }
    elseif (class_exists($commonClass)) {
      return new $commonClass($this);
    }
    else {
      throw new NotLoggableError("Module '{$this->module}' not found. class '$adminClass'");
    }
  }

  protected function auth() {
    Auth::$errorsText = [
      Auth::ERROR_AUTH_NO_LOGIN        => Lang::get('auth.noLogin'),
      Auth::ERROR_AUTH_USER_NOT_ACTIVE => Lang::get('auth.userNotActive'),
      Auth::ERROR_AUTH_WRONG_PASS      => Lang::get('auth.wrongPass')
    ];
    parent::auth();
  }

}