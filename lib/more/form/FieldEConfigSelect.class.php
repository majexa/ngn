<?php

class FieldEConfigSelect extends FieldESelect {

  protected function init() {
    $this->options['options'] = Config::getVar('fieldE/'.$this->options['name']);
    parent::init();
  }

}