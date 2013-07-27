<?php

class FieldEStatic extends FieldEText {

  public $options = [
    'noRowHtml' => true
  ];

  function html() {
    return '';
  }

}
