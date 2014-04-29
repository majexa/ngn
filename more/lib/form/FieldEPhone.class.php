<?php

class FieldEPhone extends FieldEText {

  public $useTypeJs = true;

  protected function defineOptions() {
    return [
      'cssClass' => 'validate-phone',
      'help'     => 'Пример: +79202123933'
    ];
  }

  protected function prepareValue() {
    if (empty($this->options['value'])) return;
    $this->options['value'] = trim($this->options['value'], '+ ');
  }

  protected function prepareInputValue($value) {
    return '+'.$value;
  }

  protected function validate2() {
    if (!Misc::validPhone('+'.$this->options['value'])) $this->error = "Неправильный формат телефона";
  }

}