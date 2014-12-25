<?php

class FieldEText extends FieldEInput {

  public $inputType = 'text';

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['maxlength' => 255]);
  }

  protected function prepareValue() {
    parent::prepareValue();
    if (!isset($this->options['value'])) $this->options['value'] = '';
  }

}
