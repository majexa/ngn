<?php

class UserRegForm extends UserForm {

  public $create = true;
  
  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'name' => 'userReg',
      'submitTitle' => Locale::get('register'),
      'active' => !Config::getVarVar('userReg', 'activation')
    ]);
  }

  function id() {
    return 'formUserReg';
  }

  protected function _update(array $data) {
    $data = Arr::filterByKeys($data, $this->filterFields);
    $data['active'] = $this->options['active'];
    $id = DbModelCore::create('users', $data);
    if (!empty($this->options['onCreate'])) $this->options['onCreate']($id);
    Ngn::fireEvent('users.new', $id);
  }

}