<?php

class FieldEEmpty extends FieldEAbstract {

  public $options = [
    'noRowHtml' => true,
    'noValue' => true
  ];
  
  function _html() {
    return '';
  }
  
}
