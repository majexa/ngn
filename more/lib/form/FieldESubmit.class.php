<?php

class FieldESubmit extends FieldEInput {

  public $inputType = 'submit';

  protected $cssClasses = ['btn basicBtn'];

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'name' => 'btnSubmit',
      'noTitle' => true,
      'noValue' => true,
      'type' => 'submit'
    ]);
  }

  function value() {
    return null;
  }

}
