<?php

class DdFieldTypeProcent extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Проценты',
      'order'    => 140
    ];
  }

}