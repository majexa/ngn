<?php

class UserEditPhoneForm extends UserRegPhoneConfirmForm {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'submitTitle' => 'Изменить'
    ]);
  }

}