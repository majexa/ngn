<?php

class UsersEditForm extends UsersEditFormAbstract {

  protected function _update(array $data) {
    parent::_update($data);
    Auth::save(DbModelCore::get('users', $this->userId));
  }

}
