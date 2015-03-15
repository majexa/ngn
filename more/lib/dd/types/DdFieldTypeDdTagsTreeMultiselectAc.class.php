<?php

class DdFieldTypeDdTagsTreeMultiselectAc extends DdFieldTypeSelectTagsStructure {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Древовидный выбор нескольких тэгов (автодополнение)',
      'descr'    => 'Выбираемые в контроле теги выводятся с родительским тегом',
      'order'    => 250,
    ];
  }

}