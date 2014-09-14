<?php

class DdFieldTypeTypoTextarea extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Многострочное поле',
      'order'  => 100
    ];
  }

}