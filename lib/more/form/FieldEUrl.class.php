<?php

class FieldEUrl extends FieldEText {

  protected function defineOptions() {
    $this->options['cssClass'] = 'validate-url';
  }

  function value() {
    return rtrim(parent::value(), '/');
  }
  
  protected function validate2() {
    if (!Misc::validUrl($this->options['value'])) $this->error = 'Неправильный формат ссылки';
  }

}