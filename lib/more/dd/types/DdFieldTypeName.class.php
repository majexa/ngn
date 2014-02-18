<?php

class DdFieldTypeName extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Имя',
      'order'    => 120
    ];
  }

}