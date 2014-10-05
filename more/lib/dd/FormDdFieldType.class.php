<?php

class FormDdFieldType extends Form {

  function __construct() {
    $options = Arr::get(DdFieldCore::getTypes(), 'title', 'KEY');
    unset($options['invalid']);
    parent::__construct([[
      'title' => '',
      'name' => 'type',
      'type' => 'imagedRadio',
      'options' => $options,
      'jsOptions' => [
        'maxLableLength' => 7
      ]
    ]]);
  }

}