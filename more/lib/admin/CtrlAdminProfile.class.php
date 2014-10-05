<?php

class CtrlAdminProfile extends CtrlAdmin {

  static $properties = [
    'title'  => 'Профиль',
    'order'  => 320,
    'onMenu' => false
  ];

  function action_default() {
    $form = new Form(new Fields([
      [
        'title'    => Lang::get('login'),
        'name'     => 'login',
        'type'     => 'text',
        'required' => true
      ],
      [
        'title'    => Lang::get('email'),
        'name'     => 'email',
        'type'     => 'text',
        'required' => true
      ],
      [
        'title' => Lang::get('password'),
        'help'  => 'Оставьте поле пустым, если не хотите менять пароль',
        'name'  => 'pass',
        'type'  => 'password',
      ],
    ]), ['filterEmpties' => true]);
    $form->setElementsData(DbModelCore::get('users', $this->userId)->getClean());
    $this->d['form'] = $form->html();
    if ($form->isSubmittedAndValid()) {
      DbModelCore::update('users', $this->userId, $form->getData());
      $this->redirect();
    }
    $this->d['tpl'] = 'users/edit-my-account-2';
  }

}