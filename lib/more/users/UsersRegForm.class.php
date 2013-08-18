<?php

class UsersRegForm extends UsersForm {

  public $create = true;
  
  protected function defineOptions() {
    return [
      'name' => 'userReg',
      'submitTitle' => 'Зарегистрироваться',
      'active' => !Config::getVarVar('userReg', 'activation')
    ];
  }

  protected function _getFields() {
    $fields = parent::_getFields();
    if (Config::getVarVar('userReg', 'phoneConfirm')) {
      $fields[] = [
        'name' => 'code',
        'type' => 'hidden'
      ];
    }
    return $fields;
  }

  protected function initCodeError() {
    if (!Config::getVarVar('userReg', 'phoneConfirm')) return;
    $codeEl = $this->getElement('code');
    $exists = db()->selectCell('SELECT id FROM userPhoneConfirm WHERE phone=? AND code=?', $this->getElement('phone')->value(), $codeEl->value());
    if (!$exists) $this->globalError('Неверный код подтверждения');
  }

  protected function initErrors() {
    parent::initErrors();
    $this->initCodeError();
  }

  protected function _update(array $data) {
    $data = Arr::filterByKeys($data, $this->filterFields);
    $data['active'] = $this->options['active'];
    $id = DbModelCore::create('users', $data);
    Ngn::fireEvent('users.new', $id);
  }

}