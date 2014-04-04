<?php

class DdFieldTypeDdTags extends DdFieldTypeSelectTagsStructure {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Тэги (через запятую)',
      'order'    => 210,
    ];
  }

}