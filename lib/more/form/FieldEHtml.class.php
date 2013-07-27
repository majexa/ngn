<?php

class FieldEHtml extends FieldEAbstract {

  static $requiredOptions = ['html'];
  
  public $options = [
    'noRowHtml' => true,
    'noValue' => true
  ];
  
  function _html() {
    return $this->options['html'];
  }

}
