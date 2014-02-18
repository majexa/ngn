<?php

class DdFieldTypeStatic extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Статический текст',
      'virtual'  => true,
      'order'    => 150
    ];
  }

}