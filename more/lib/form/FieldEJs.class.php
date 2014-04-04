<?php

class FieldEJs extends FieldEAbstract {

  public $options = [
    'noRowHtml' => true,
    'noValue' => true
  ];
  
  function _js() {
    return str_replace('{formId}', "'".$this->form->id()."'", $this->options['js']);
  }

}
