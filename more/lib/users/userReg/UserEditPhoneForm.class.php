<?php

class UserEditPhoneForm extends UserRegPhoneForm {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'submitTitle' => 'Изменить'
    ]);
  }

}