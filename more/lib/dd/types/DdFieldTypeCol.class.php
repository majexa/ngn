<?php

class DdFieldTypeCol extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Колонка',
      'order'    => 20
    ];
  }

}