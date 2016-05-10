<?php

class CtrlCommonUserLostPass extends CtrlBase {

  protected function getParamActionN() {
    return 2;
  }

  protected function init() {
    $this->d['tpl'] = 'users/lostpass';
  }

  protected function getLostPassForm() {
    $form = new Form([
      [
        'name'     => 'email',
        'title'    => 'E-mail',
        'type'     => 'email',
        'required' => true
      ]
    ]);
    //$form->action = '/default/userLostPass';
    return $form;
  }

  function action_default() {
    $this->setPageTitle(Lang::get('forgetPassword?'));
    $form = $this->getLostPassForm();
    $form->options['submitTitle'] = Lang::get('send');
    $this->d['form'] = $form->html();
    if ($form->isSubmittedAndValid()) {
      $r = UsersCore::sendLostPass($form->getData()['email']);
      $this->redirect($this->tt->getPath($this->getParamActionN()).'/'.($r ? 'complete' : 'failed'));
    }
  }

  function action_failed() {
  }

  function action_complete() {
    $this->setPageTitle(Lang::get('emailSent'));
  }

}