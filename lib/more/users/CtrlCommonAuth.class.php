<?php

class CtrlCommonAuth extends CtrlCammon {
use CtrlFormTabs;
  
  protected function init() {
    $this->d['mainTpl'] = 'ajax';
  }

  function action_default() {
    $this->d['tpl'] = 'common/auth-ajax';
  }

  function action_ajax_auth() {
    $urls = ['/c/auth/json_form'];
    if (Config::getVarVar('userReg', 'enable')) {
      $urls[] = Config::getVarVar('userReg', 'phoneConfirm') ? '/c/userRegPhone/json_form' : '/c/userReg/json_form';
    }
    $this->processFormTabs($urls);
  }

  function action_json_form() {
    $form = new AuthForm;
    $form->action = '/c/auth/json_form';
    $this->json['title'] = 'Авторизация';
    if ($form->isSubmittedAndValid()) {
      $this->json['success'] = true;
      return;
    }
    return $this->jsonFormAction($form);
  }
  
  function action_ajax_top() {
    $this->ajaxOutput = $this->path->getTpl('top', ['path' => $this->req->rq('path')]);
  }

  function action_keyLogin() {
    Auth::save(DbModelCore::get('users', $this->req->param(3), 'actCode'));
    $this->redirect($this->req['url']);
  }
  
}
