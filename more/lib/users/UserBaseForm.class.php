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
    $this->uc->check('email', 'Такой имейл уже зарегистрирован');
    $this->uc->check('login', Config::getVarVar('userReg', 'loginAsFullName') ? 'Такое Ф.И.О. уже зарегистрировано' : 'Такой логин уже зарегистрирован');
    $this->uc->check('phone', 'Пользователь с таким телефоном уже существует');
  }

  protected function initCodeError() {
    if (!Config::getVarVar('userReg', 'phoneConfirm')) return;
    $codeEl = $this->getElement('code');
    if (getConstant('TESTING') and $codeEl->value() == '123') return;
    $exists = db()->selectCell('SELECT id FROM userPhoneConfirm WHERE phone=? AND code=?', $this->getElement('phone')->value(), $codeEl->value());
    if (!$exists) $this->globalError('Неверный код подтверждения');
  }

}
