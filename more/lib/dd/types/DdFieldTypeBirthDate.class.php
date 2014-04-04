<?php

class DdFieldTypeBirthDate extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'DATE',
      'title'  => 'Дата рождения',
      'order'  => 90
    ];
  }

}