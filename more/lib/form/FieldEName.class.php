<?php

class FieldEName extends FieldEText {

  protected function defineOptions() {
    return [
      'cssClass' => 'validate-name'
    ];
  }
  
  protected function validate2() {
    if (!Misc::validName($this->options['value'])) $this->error('Неправильный формат');
  }

}