<?php

class DdFieldTypeFile extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Файл',
      'order'    => 40
    ];
  }

}