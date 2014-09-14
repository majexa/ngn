<?php

class DdFieldTypeUser extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Пользователь',
      'order'    => 230
    ];
  }

}