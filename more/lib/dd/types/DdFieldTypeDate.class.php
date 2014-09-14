<?php

class DdFieldTypeDate extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'DATE',
      'title'  => 'Дата',
      'order'  => 70
    ];
  }

}