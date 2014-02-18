<?php

class DdFieldTypeDate extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'DATE',
      'title'  => 'Дата',
      'order'  => 70
    ];
  }

}