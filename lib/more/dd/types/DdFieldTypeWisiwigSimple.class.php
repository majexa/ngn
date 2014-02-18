<?php

class DdFieldTypeWisiwigSimple extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Текстовое поле с базовым визуальным редактором',
      'order'  => 111
    ];
  }

}