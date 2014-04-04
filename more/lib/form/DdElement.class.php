<?php

trait DdElement {

  protected $strName;

  protected function allowedFormClass() {
    return 'DdForm';
  }

  protected function beforeInit() {
    $this->strName = isset($this->form) ? $this->form->strName : Misc::checkEmpty($this->options['strName']);
  }

}