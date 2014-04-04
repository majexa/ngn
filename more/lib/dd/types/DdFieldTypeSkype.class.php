<?php

class DdFieldTypeSkype extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Skype',
      'order'    => 200
    ];
  }

}