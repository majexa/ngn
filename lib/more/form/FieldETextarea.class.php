<?php

class FieldETextarea extends FieldEText {

  protected $staticType = 'textarea';
  protected $useDefaultJs = true;

  public $options = [
    'maxlength' => 65000
  ];
  
  function _html() {
    return '<textarea name="'.$this->options['name'].'"'.
      Tt()->tagParams($this->getTagsParams()).$this->getClassAtr().'>'.
      $this->options['value'].'</textarea>';
  }
  
}
