<?php

class DdFieldTypeWisiwig extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Текстовое поле с визуальным редактором (с поддержкой вложенных изображений, файлов, таблиц и пр.)',
      'order'  => 110
    ];
  }

}