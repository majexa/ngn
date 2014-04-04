<?php

class DdFieldTypeDdItemSelect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Выбор dd-записи',
      'order'    => 300,
      'fields'   => [
        [
          'type'  => 'ddStructure',
          'title' => 'Структура',
          'name'  => 'strName'
        ]
      ]
    ];
  }

}