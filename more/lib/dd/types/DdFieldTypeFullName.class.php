<?php

class DdFieldTypeFullName extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'title'    => 'Ф.И.О.',
      'dbLength' => 255,
      'order'    => 90
    ];
  }

}