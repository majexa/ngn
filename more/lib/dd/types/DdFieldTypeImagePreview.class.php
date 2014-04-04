<?php

class DdFieldTypeImagePreview extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Изображение',
      'order'    => 50
    ];
  }

}