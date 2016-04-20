<?php

class CtrlCommonUserLostPass extends CtrlBase {

  protected function getParamActionN() {
    return 2;
  }

  protected function init() {
    $this->d['tpl'] = 'users/lostpass';
  }

  static function getLoasPassForm() {
    $form = new Form([
      [
        'name'     => 'email',
        'title'    => 'E-mail',
        'type'     => 'email',
        'required' => true
      ]
    ]);
    $form->action = '/default/userLostPass';
    return $form;
  }

  function action_default() {
    $this->setPageTitle(Lang::get('forgetPassword?'));
    $form = self::getLoasPassForm();
    $form->options['submitTitle'] = Lang::get('send');
    $this->d['form'] = $form->html();
    if ($form->isSubmittedAndValid()) {
      $r = UsersCore::sendLostPass($form->getData()['email']);
      $this->redirect($this->tt->getPath(2).'/'.($r ? 'complete' : 'failed'));
    }
  }

  function action_failed() {
  }

  function action_complete() {
    $this->setPageTitle(Lang::get('emailSent'));
  }

}