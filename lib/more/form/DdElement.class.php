<?php

trait DdElement {

  protected $strName;

  protected function allowedFormClass() {
    return 'DdForm';
  }

  protected function beforeInit() {
    $this->strName = isset($this->oForm) ? $this->oForm->strName : Misc::checkEmpty($this->options['strName']);
  }

}