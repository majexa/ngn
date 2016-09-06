<?php

class CtrlCommonVkAuth extends CtrlBase {

  protected function getParamActionN() {
    return 2;
  }

  function action_ajax_exists() {
    $this->ajaxSuccess = (bool)DbModelCore::get('users', $this->req['login'], 'login');
  }

  function action_ajax_reg() {
    $id = DbModelCore::create('users', [
      'active' => 1,
      'login' => $this->req->rq('login')
    ]);
    Auth::loginById($id);
    $this->ajaxSuccess = true;
  }

  function action_ajax_auth() {
    $this->ajaxSuccess = (bool)Auth::loginByLogin($this->req->rq('login'));
  }

}