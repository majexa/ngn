<?php

class DdFieldTypeUrl extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Одна ссылка',
      'order'  => 170
    ];
  }

}