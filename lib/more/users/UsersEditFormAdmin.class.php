<?php

class UsersEditFormAdmin extends UsersEditForm {

  protected function init() {
    parent::init();
    $this->filterFields[] = 'role';
  }

  protected function extraFieldsOptions() {
    return ['getDisallowed' => true];
  }

}
