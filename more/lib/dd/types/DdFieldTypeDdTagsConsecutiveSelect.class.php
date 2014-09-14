<?php

class DdFieldTypeDdTagsConsecutiveSelect extends DdFieldTypeSelectTagsStructure {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Последовательный выбор тэга',
      'order'    => 260,
    ];
  }

}