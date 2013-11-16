<?php

class CtrlCommonUserLostPass extends CtrlCammon {

  protected function init() {
    $this->d['tpl'] = 'users/lostpass';
  }

  function action_default() {
    $this->setPageTitle('Забыли пароль?');
    $form = new Form([
      [
        'name'     => 'email',
        'title'    => 'E-mail',
        'type'     => 'email',
        'required' => true
      ]
    ]);
    $form->options['submitTitle'] = 'Выслать';
    $this->d['form'] = $form->html();
    if ($form->isSubmittedAndValid()) {
      $r = UsersCore::sendLostPass($form->getData()['email']);
      $this->redirect($this->path->getPath(2).'/'.($r ? 'complete' : 'failed'));
    }
  }

  function action_failed() {
  }

  function action_complete() {
    $this->setPageTitle('Готово');
  }

}