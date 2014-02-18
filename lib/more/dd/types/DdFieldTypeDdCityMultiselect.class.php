<?php

class DdFieldTypeDdCityMultiselect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Город мультивыбор',
      'order'    => 291,
    ];
  }

}