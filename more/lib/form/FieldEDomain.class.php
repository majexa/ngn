<?php

class FieldEDomain extends FieldEText {

  protected function init() {
    $this->cssClasses[] = 'validate-domain';
    parent::init();
  }
  
  function value() {
    return strtolower(parent::value());
  }

}
