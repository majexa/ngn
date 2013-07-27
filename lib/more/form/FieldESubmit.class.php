<?php

class FieldESubmit extends FieldEInput {

  public $inputType = 'submit';
  
  public $options = [
    'noTitle' => true,
    'noValue' => true,
    'type' => 'submit'
  ];
  
  function value() {
    return null;
  }

}
