<?php

class DdFieldTypeDdItemsSelect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'INT',
      'dbLength' => 11,
      'title'    => 'Выбор dd-записей',
      'order'    => 300,
      'fields'   => [
        [
          'type'  => 'ddStructure',
          'title' => 'Структура',
          'name'  => 'strName'
        ],
        [
          'type'  => 'ddStructure',
          'title' => 'По какому полю фильтровать',
          'name'  => 'filterField'
        ],
      ]
    ];
  }

}