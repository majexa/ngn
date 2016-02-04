<?php

class FieldEUserRole extends FieldESelect {

  protected function init() {
    $this->options['options'] = Config::getVar('userRoles');
    parent::init();
  }

}