<?php

class DdFieldTypeDdTagsMultiselectDropdown extends DdFieldTypeSelectTagsStructure {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Выпадающий выбор нескольких тэгов',
      'order'    => 230,
      'tags'     => true
    ];
  }

}