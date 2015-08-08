<?php

class DdFieldTypeDdTagsAc extends DdFieldTypeSelectTagsStructure {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Тэги (одноуровневые, автокоплит)',
      'order'    => 210,
    ];
  }

}