<?php

class DdFieldTypeDdUserImage extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'VARCHAR',
      'dbLength' => 255,
      'title' => 'Изображение пользователя',
      'order' => 210
    ];
  }

}