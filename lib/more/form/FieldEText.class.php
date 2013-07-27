<?php

class FieldEText extends FieldEInput {

  public $inputType = 'text';

  protected function defineOptions() {
    parent::defineOptions();
    $this->options['maxlength'] = 255;
  }

  protected function prepareValue() {
    parent::prepareValue();
    if (!isset($this->options['value'])) $this->options['value'] = '';
  }
}
