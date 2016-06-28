<?php

class UserFormPartial extends Form {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'class' => 'formUser'
    ]);
  }

}