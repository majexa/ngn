<?php

class DdFieldTypeDdTagsTreeSelect extends DdFieldTypeSelectTagsStructure {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Древовидный выбор одного тэга',
      'order'    => 240,
    ];
  }

}