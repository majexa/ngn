<?php

class FieldERadio extends FieldECheckbox {

  public $inputType = 'radio';

  public $options = [
    'type' => 'radio'
  ];
  
  protected function initDefaultValue() {
    $this->defaultValue = Arr::firstKey($this->options['options']);
  }

}