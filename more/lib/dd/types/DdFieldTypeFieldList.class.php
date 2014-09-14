<?php

class DdFieldTypeFieldList extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Набор текстовых полей',
      'order'  => 110
    ];
  }

}