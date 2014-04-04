<?php

class DdFieldTypeConfigSelect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'title'    => 'Список из конфигурации',
      'dbLength' => 255,
      'order'    => 90
    ];
  }

}