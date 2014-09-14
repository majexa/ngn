<?php

class DdFieldTypeDomain extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Домен',
      'order'    => 120
    ];
  }

}