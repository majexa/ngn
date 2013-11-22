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
    $urls = ['/default/auth/json_form'];
    if (Config::getVarVar('userReg', 'enable')) {
      $urls[] = Config::getVarVar('userReg', 'phoneConfirm') ? '/default/userRegPhone/json_form' : '/default/userReg/json_form';
    }
    $this->processFormTabs($urls);
  }

  function action_json_form() {
    LogWriter::str('aaa', 1);
    $form = new AuthForm;
    $form->action = '/default/auth/json_form';
    $this->json['title'] = 'Авторизация ';
    LogWriter::str('aaa', 2);
    if ($form->isSubmittedAndValid()) {
      $this->json['success'] = true;
      LogWriter::str('aaa', 3);
      return;
    }
    LogWriter::str('aaa', 4);
    $this->json['req'] = $form->req->r;
    $this->json['req2'] = $_REQUEST;
    $this->json['valid'] = ($form->validate() ? 'true' : $form->lastError);
    LogWriter::str('aaa', 5);
    if ($form->req['formId']) $this->json['idssss'] = $form->req['formId'].' - '.$form->id();
    LogWriter::str('aaa', 6);
    return $this->jsonFormAction($form);
  }
  
  function action_ajax_top() {
    $this->ajaxOutput = $this->tt->getTpl('top', ['path' => $this->req->rq('path')]);
  }

  function action_keyLogin() {
    Auth::save(DbModelCore::get('users', $this->req->param(3), 'actCode'));
    $this->redirect($this->req['url']);
  }
  
}
