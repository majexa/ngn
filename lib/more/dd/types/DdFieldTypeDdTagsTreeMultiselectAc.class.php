<?php

class DdFieldTypeDdTagsTreeMultiselectAc extends DdFieldTypeSelectTagsStructure {

  static protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Древовидный выбор нескольких тэгов (автокомплит)',
      'order'    => 250,
    ];
  }

}