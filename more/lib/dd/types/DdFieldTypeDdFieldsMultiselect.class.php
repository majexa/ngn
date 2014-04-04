<?php

class DdFieldTypeDdFieldsMultiselect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Выбор нескольких dd-полей',
      'order'  => 300,
      'fields' => [
        [
          'type'  => 'ddStructure',
          'title' => 'Структура',
          'name'  => 'strName'
        ],
        [
          'type'  => 'bool',
          'title' => 'показывать системные',
          'name'  => 'getSystem'
        ],
        [
          'type'  => 'bool',
          'title' => 'показывать неразрешенные',
          'name'  => 'getDisallowed'
        ],
      ]
    ];
  }

}