<?php

class CtrlCommonAuth extends CtrlDefault {

  protected function getParamActionN() {
    return 2;
  }

  protected function init() {
    $this->d['mainTpl'] = 'ajax';
  }

  function action_default() {
    $this->d['tpl'] = 'common/auth-ajax';
  }

  function action_json_auth() {
    $urls = ['/'.Sflm::frontendName(true).'/auth/json_form'];
    if (Config::getVarVar('userReg', 'enable')) {
      $urls[] = Config::getVarVar('userReg', 'phoneConfirm') ? //
        ('/'.Sflm::frontendName(true).'/userRegPhone/json_form') : //
        ('/'.Sflm::frontendName(true).'/userReg/json_form');
    }
    $this->processFormTabs($urls);
    if (Config::getVarVar('userReg', 'vkAuthEnable')) {
      $this->json['tabs'] .= <<<HTML
  <h2 class="tab" title="Войти с помощью «Вконтакте»" data-name="vk">
    <img src="/i/img/icons/vk.png" />
  </h2>
  <div id="vkAuth"></div>
HTML;
    }

  }

  function action_json_form() {
    $form = new AuthForm;
    $form->action = '/'.Sflm::frontendName(true).'/auth/json_form';
    $this->json['title'] = 'Авторизация';
    if ($form->isSubmittedAndValid()) {
      $this->json['success'] = true;
      return null;
    }
    $this->json['req'] = $form->req->r;
    $this->json['valid'] = ($form->validate() ? 'true' : $form->lastError);
    if ($form->req['formId']) $this->json['idssss'] = $form->req['formId'].' - '.$form->id();
    return $form;
  }

  function action_ajax_top() {
    $this->ajaxOutput = $this->tt->getTpl('top', ['path' => $this->req->rq('path')]);
  }

  function action_keyLogin() {
    Auth::save(DbModelCore::get('users', $this->req->param(3), 'actCode'));
    $this->redirect($this->req['url']);
  }

}
