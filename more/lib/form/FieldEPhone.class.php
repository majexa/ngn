<?php

class FieldEPhone extends FieldEText {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
      'cssClass' => 'validate-phone',
      'help'     => 'Формат: +71234567890'
    ]);
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