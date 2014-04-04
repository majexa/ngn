<?php

class FieldENum extends FieldEText {
use ElNumberLimits;

  public $options = [
    'cssClass' => 'validate-integer'
  ];

  protected function prepareValue() {
    if (!empty($this->options['value'])) $this->options['value'] = (int)$this->options['value'];
  }

  function isEmpty() {
    return false; // что бы нулевое значение не заменялось на null
  }

  protected function init() {
    parent::init();
    $this->numberLimits();
  }

}
