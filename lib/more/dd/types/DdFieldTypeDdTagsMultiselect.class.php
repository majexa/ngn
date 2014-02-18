<?php

class DdFieldTypeDdTagsMultiselect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Выбор нескольких тэгов',
      'order'    => 230
    ];
  }

}