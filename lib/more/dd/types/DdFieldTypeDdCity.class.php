<?php

class DdFieldTypeDdCity extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'VARCHAR',
      'dbLength' => 255,
      'title' => 'Город',
      'order' => 290,
    ];
  }

}