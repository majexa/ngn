<?php

class DdFieldTypeNumberRange extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'            => 'INT',
      'dbLength'          => 15,
      'title'             => 'Диапозон чисел',
      'order'             => 90000,
      'disableTypeChange' => true
    ];
  }

}