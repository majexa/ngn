<?php

class UsersEditFormAdmin extends UsersEditFormAbstract {

  protected function init() {
    parent::init();
    $this->filterFields[] = 'role';
    $this->options['title'] = Locale::get('userEdit', 'users');
  }

  protected function extraFieldsOptions() {
    return ['getDisallowed' => true];
  }

}
