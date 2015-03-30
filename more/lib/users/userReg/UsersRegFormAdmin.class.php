<?php

class UsersRegFormAdmin extends UserRegForm {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'title' => 'Создание пользователя',
      'submitTitle' => 'Создать',
    ]);
  }

  protected function initCodeError() {}

}