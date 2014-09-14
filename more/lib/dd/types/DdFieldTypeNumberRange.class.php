<?php

class DdFieldTypeNumberRange extends DdFieldType {

  protected function _get() {
    return [
      'dbType'            => 'INT',
      'dbLength'          => 15,
      'title'             => 'Диапозон чисел',
      'order'             => 90000,
      'disableTypeChange' => true
    ];
  }

}