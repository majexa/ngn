<?php

class DdFieldTypeNum extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Целое число',
      'order'    => 120
    ];
  }

}