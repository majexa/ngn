<?php

class UsersRegFormAdmin extends UsersRegForm {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'submitTitle' => 'Создать',
    ]);
  }

  protected function initCodeError() {}

}