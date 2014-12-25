<?php

class FieldEName extends FieldEText {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'cssClass' => 'validate-name'
    ]);
  }
  
  protected function validate2() {
    if (!Misc::validName($this->options['value'])) $this->error('Неправильный формат');
  }

}