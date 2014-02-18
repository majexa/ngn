<?php

class DdFieldTypeUser extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Пользователь',
      'order'    => 230
    ];
  }

}