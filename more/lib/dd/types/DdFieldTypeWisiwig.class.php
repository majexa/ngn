<?php

class DdFieldTypeWisiwig extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Текстовое поле с визуальным редактором',
      'descr'  => 'С поддержкой вложенных изображений, файлов, таблиц и пр.',
      'order'  => 110
    ];
  }

}