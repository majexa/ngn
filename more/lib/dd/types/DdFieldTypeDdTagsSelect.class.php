<?php

class DdFieldTypeDdTagsSelect extends DdFieldTypeSelectTagsStructure {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Выбор одного тэга',
      'order'    => 220
    ];
  }

}