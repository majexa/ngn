<?php

class DdFieldTypeDdTagsTreeMultiselect extends DdFieldType {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Древовидный выбор нескольких тэгов',
      'order'    => 250,
    ];
  }

}