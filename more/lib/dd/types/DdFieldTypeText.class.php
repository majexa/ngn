<?php

class DdFieldTypeText extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Однострочное поле',
      'order'    => 20
    ];
  }

}