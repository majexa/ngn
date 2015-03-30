<?php

class CtrlCommonAuth extends CtrlCammon {

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
  }

  protected function processFormTabs(array $paths, $tpl = 'common/dialogFormTabs') {
    foreach ($paths as $uri) {
      $ctrl = (new RouterManager([
        'req' => new Req([
          'uri'              => $uri,
          'disableSflmStore' => true
        ])
      ]))->router()->dispatch()->controller;
      if (empty($ctrl->json['form'])) {
        throw new Exception("no form by uri '$uri'");
      }
      $form = [
        'id'    => Html::getParam($ctrl->json['form'], 'id'),
        'title' => $ctrl->json['title'],
        'html'  => $ctrl->json['form']
      ];
      if ($ctrl->actionResult) $form['submitTitle'] = $ctrl->actionResult->options['submitTitle'];
      $d['forms'][] = $form;
    }
    $this->json['tabs'] = $this->tt->getTpl('common/auth-ajax', $d);
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
