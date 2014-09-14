<?php

class DdFieldTypeText extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Одностройчное поле',
      'order'    => 20
    ];
  }

}