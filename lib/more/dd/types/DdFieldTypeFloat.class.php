<?php

class DdFieldTypeFloat extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'float',
      'dbLength' => 11,
      'title'    => 'Дробное число',
      'order'    => 130
    ];
  }

}