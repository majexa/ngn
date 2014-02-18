<?php

class DdFieldTypeSound extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Аудио',
      'order'    => 220
    ];
  }

}