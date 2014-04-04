<?php

class DdFieldTypeDdUserImage extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'VARCHAR',
      'dbLength' => 255,
      'title' => 'Изображение пользователя',
      'order' => 210
    ];
  }

}