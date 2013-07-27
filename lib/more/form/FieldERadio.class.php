<?php

class FieldERadio extends FieldECheckbox {

  public $inputType = 'radio';

  public $options = [
    'type' => 'radio'
  ];
  
  protected function initDefaultValue() {
    $this->defaultValue = Arr::first_key($this->options['options']); 
  }

}