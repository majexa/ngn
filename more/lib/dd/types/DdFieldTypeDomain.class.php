<?php

class DdFieldTypeDomain extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Домен',
      'order'    => 120
    ];
  }

}