<?php

class DdFieldTypePhone extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Телефон',
      'order'    => 210
    ];
  }

}