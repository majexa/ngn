<?php

class DdFieldTypeEmail extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'E-mail',
      'order'    => 60
    ];
  }

}