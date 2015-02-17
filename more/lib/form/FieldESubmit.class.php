<?php

class FieldESubmit extends FieldEInput {

  public $inputType = 'submit';

  protected $cssClasses = ['btn basicBtn'];
  
  public $options = [
    'noTitle' => true,
    'noValue' => true,
    'type' => 'submit'
  ];

  function value() {
    return null;
  }

}
