<?php

class FieldEFloat extends FieldEText {
use ElNumberLimits;

  protected function prepareValue() {
    if (!empty($this->options['value']))
      $this->options['value'] = floatval(str_replace(',', '.', $this->options['value']));
  }

  protected function init() {
    parent::init();
    $this->numberLimits();
  }

}