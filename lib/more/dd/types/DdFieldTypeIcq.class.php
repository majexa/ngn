<?php

class DdFieldTypeIcq extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 15,
      'title'    => 'ICQ#',
      'order'    => 190
    ];
  }

}