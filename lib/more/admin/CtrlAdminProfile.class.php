<?php

class CtrlAdminProfile extends CtrlAdmin {

  static $properties = [
    'title' => 'Профиль',
    'order' => 320,
    'onMenu' => true
  ];
  
  function action_default() {
    $oF = new Form(new Fields([
      [
        'title' => LANG_LOGIN,
        'name' => 'login',
        'type' => 'text',
        'required' => true
      ],
      [
        'title' => LANG_EMAIL,
        'name' => 'email',
        'type' => 'text',
        'required' => true
      ],
      [
        'title' => LANG_PASSWORD,
        'help' => 'Оставьте поле пустым, если не хотите менять пароль',
        'name' => 'pass',
        'type' => 'password',
        // 'required' => false,
      ],
    ]), ['filterEmpties' => true]);
    $oF->setElementsData(DbModelCore::get('users', $this->userId)->getClear());
    $this->d['form'] = $oF->html();
    if ($oF->isSubmittedAndValid()) {
      DbModelCore::update('users', $this->userId, $oF->getData());
    }
    $this->d['tpl'] = 'users/edit-my-account-2';
  }
  
  function action_notifySettings() {
    $oF = new Notify_SendMethodForm();
    if ($oF->update()) {
      $this->redirect();
      return;
    }
    $this->d['form'] = $oF->html();
    $this->d['tpl'] = 'users/notifySettings';
  }

}