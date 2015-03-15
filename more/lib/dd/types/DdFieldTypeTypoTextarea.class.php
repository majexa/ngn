<?php

class DdFieldTypeTypoTextarea extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Многострочное поле',
      'descr'  => 'С типографированием',
      'order'  => 100
    ];
  }

}