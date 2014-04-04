<?php

class DdFieldTypePrice extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'FLOAT',
      'dbLength' => 11,
      'title'    => 'Деньги',
      'order'    => 140
    ];
  }

}