<?php

class DdFieldTypeVideo extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Видео',
      'order'    => 220
    ];
  }

}