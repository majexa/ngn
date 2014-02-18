<?php

class DdFieldTypeDdTagsConsecutiveSelect extends DdFieldTypeSelectTagsStructure {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Последовательный выбор тэга',
      'order'    => 260,
    ];
  }

}