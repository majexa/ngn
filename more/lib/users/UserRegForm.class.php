<?php

class UserRegForm extends UserForm {

  public $create = true;
  
  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'name' => 'userReg',
      'submitTitle' => 'Готово',
      'active' => !Config::getVarVar('userReg', 'activation')
    ]);
  }

  function id() {
    return 'formUserReg';
  }

  protected function _getFields() {
    $fields = parent::_getFields();
    if (Config::getVarVar('userReg', 'phoneConfirm')) {
      $fields[] = [
        'name' => 'code',
        'type' => 'hidden'
      ];
      $fields[] = [
        'name' => 'phone',
        'type' => 'hidden'
      ];
    }
    return $fields;
  }

  protected function _update(array $data) {
    $data = Arr::filterByKeys($data, $this->filterFields);
    $data['active'] = $this->options['active'];
    $id = DbModelCore::create('users', $data);
    if (!empty($this->options['onCreate'])) $this->options['onCreate']($id);
    Ngn::fireEvent('users.new', $id);
  }

  protected function initErrors() {
    parent::initErrors();
    $this->initCodeError();
  }

}