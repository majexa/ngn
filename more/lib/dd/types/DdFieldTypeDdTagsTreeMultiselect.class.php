<?php

class DdFieldTypeDdTagsTreeMultiselect extends DdFieldTypeSelectTagsStructure {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Древовидный выбор нескольких тэгов',
      'order'    => 250,
    ];
  }

}