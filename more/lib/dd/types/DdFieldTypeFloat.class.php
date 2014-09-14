<?php

class DdFieldTypeFloat extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'float',
      'dbLength' => 11,
      'title'    => 'Дробное число',
      'order'    => 130
    ];
  }

}