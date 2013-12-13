<?php

DdFieldCore::registerType('ddMetro', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Метро',
  'order'    => 300,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdMetro extends FieldEDdTagsTreeMultiselect {

  function _html() {
    parent::_html();
    $data = self::getTplData(new DdTagsTagsTree(new DdTagsGroup($this->strName, $this->options['name'])), $this->options['name'], $this->options['value'], $this->options['rootTagId']);
     return Tt()->getTpl('dd/tagsTreeMultiselect', array_merge($data, ['dataParams' => isset($this->options['dataParams']) ? $this->options['dataParams'] : []]));
  }

}


