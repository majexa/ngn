<?php

class DdFieldTypeSkype extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Skype',
      'order'    => 200
    ];
  }

}