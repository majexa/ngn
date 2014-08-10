<?php

class DdForm extends Form {

  public $strName;

  function __construct($fields, $strName, array $options = []) {
    $this->strName = $strName;
    Sflm::frontend('js')->addClass('Ngn.DdForm');
    parent::__construct($fields, $options);
  }

  protected function dataParams() {
    return [
      'class' => 'DdForm',
      'strName' => $this->strName
    ];
  }

  protected function setElementsDataDefault() {
    $r = parent::setElementsDataDefault();
    if ($r) {
      if (($paths = Hook::paths('dd/formInit'))) foreach ($paths as $path) require $path;
    }
    return $r;
  }

  protected function jsInitTagValues() {
    return "Ngn.toObj('Ngn.Form.El.DdTags.values.{$this->id()}', {});";
  }

}