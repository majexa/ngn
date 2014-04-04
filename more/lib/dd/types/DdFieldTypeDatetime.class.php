<?php

class DdFieldTypeDatetime extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'DATETIME',
      'title'  => 'Дата, время',
      'order'  => 90
    ];
  }

}