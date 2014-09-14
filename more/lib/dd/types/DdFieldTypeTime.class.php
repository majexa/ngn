<?php

class DdFieldTypeTime extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TIME',
      'title'  => 'Время',
      'order'  => 80
    ];
  }

}