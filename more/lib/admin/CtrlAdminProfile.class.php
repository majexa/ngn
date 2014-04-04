<?php

class CtrlAdminProfile extends CtrlAdmin {

  static $properties = [
    'title'  => 'Профиль',
    'order'  => 320,
    'onMenu' => true
  ];

  function action_default() {
    $form = new Form(new Fields([
      [
        'title'    => LANG_LOGIN,
        'name'     => 'login',
        'type'     => 'text',
        'required' => true
      ],
      [
        'title'    => LANG_EMAIL,
        'name'     => 'email',
        'type'     => 'text',
        'required' => true
      ],
      [
        'title' => LANG_PASSWORD,
        'help'  => 'Оставьте поле пустым, если не хотите менять пароль',
        'name'  => 'pass',
        'type'  => 'password',
      ],
    ]), ['filterEmpties' => true]);
    $form->setElementsData(DbModelCore::get('users', $this->userId)->getClear());
    $this->d['form'] = $form->html();
    if ($form->isSubmittedAndValid()) {
      DbModelCore::update('users', $this->userId, $form->getData());
      $this->redirect();
    }
    $this->d['tpl'] = 'users/edit-my-account-2';
  }

}