<?php

class UsersRegFormAdmin extends UserRegForm {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'submitTitle' => 'Создать',
    ]);
  }

  protected function initCodeError() {}

}