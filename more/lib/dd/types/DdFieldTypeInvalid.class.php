<?php

class DdFieldTypeInvalid extends DdFieldType {

  static $notList = true;

  protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 1,
      'title'    => 'Invalid',
      'order'    => 1
    ];
  }

}