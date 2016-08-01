<?php

class UsersRegFormAdmin extends UserRegForm {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'title' => Locale::get('userCreation', 'users'),
      'submitTitle' => Locale::get('create'),
    ]);
  }

  protected function initCodeError() {}

}