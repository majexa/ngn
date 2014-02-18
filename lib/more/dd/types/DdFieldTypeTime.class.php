<?php

class DdFieldTypeTime extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'TIME',
      'title'  => 'Время',
      'order'  => 80
    ];
  }

}