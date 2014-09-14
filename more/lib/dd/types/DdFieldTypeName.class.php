<?php

class DdFieldTypeName extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Имя',
      'order'    => 120
    ];
  }

}