<?php

class UserBaseForm extends Form {

  /**
   * @var FormDbUnicCheck
   */
  protected $uc;

  protected function init() {
    parent::init();
    $this->uc = new FormDbUnicCheck((new DbCond('users')), $this);
  }

  protected function initErrors() {
    $this->uc->check('email', Locale::get('suchEmailAlreadyRegistered'));
    $this->uc->check('login', Config::getVarVar('userReg', 'loginAsFullName') ? 'Такое Ф.И.О. уже зарегистрировано' : 'Такой логин уже зарегистрирован');
    $this->uc->check('phone', 'Пользователь с таким телефоном уже существует');
  }

}
