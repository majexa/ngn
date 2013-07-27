<?php

class DdForm extends Form {

  public $strName;

  function __construct($fields, $strName, array $options = []) {
    $this->strName = $strName;
    parent::__construct($fields, $options);
  }

  protected function dataParams() {
    return [
      'class' => 'DdForm',
      'strName' => $this->strName
    ];
  }

  protected function jsInitTagValues() {
    return "Ngn.toObj('Ngn.Form.El.DdTags.values.{$this->id()}', {});";
  }

}