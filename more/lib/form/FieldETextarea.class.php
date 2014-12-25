<?php

class FieldETextarea extends FieldEText {

  protected $staticType = 'textarea';

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'maxlength' => 65000,
      'useTypeJs' => true
    ]);
  }

  function _html() {
    return '<textarea name="'.$this->options['name'].'"'.
      Tt()->tagParams($this->getTagsParams()).$this->getClassAtr().'>'.
      $this->options['value'].'</textarea>';
  }
  
}
