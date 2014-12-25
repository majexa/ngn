<?php

class FieldESkype extends FieldEText {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'cssClass' => 'validate-skype'
    ]);
  }

}