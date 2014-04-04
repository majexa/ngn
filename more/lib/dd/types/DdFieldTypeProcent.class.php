<?php

class DdFieldTypeProcent extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Проценты',
      'order'    => 140
    ];
  }

}