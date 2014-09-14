<?php

class DdFieldTypeDdMetroMultiselect extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Метро мультибыбор',
      'order'    => 301,
    ];
  }

}