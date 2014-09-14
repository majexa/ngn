<?php

class DdFieldTypePrice extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'FLOAT',
      'dbLength' => 11,
      'title'    => 'Деньги',
      'order'    => 140
    ];
  }

  function sampleData() {
    return 10000;
  }

}