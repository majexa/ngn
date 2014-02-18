<?php

class DdFieldTypeDdCityRussia extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Город России',
      'order'    => 292,
      'tags'     => true,
      'tagsTree' => true
    ];
  }

}