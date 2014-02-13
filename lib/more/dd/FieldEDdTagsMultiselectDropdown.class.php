<?php

DdFieldCore::registerType('ddTagsMultiselectDropdown', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Выпадающий выбор нескольких тэгов',
  'order'    => 230,
  'tags'     => true
]);

class FieldEDdTagsMultiselectDropdown extends FieldEDdTagsMultiselect {

  protected $useTypeJs = true;

  function typeJs() {
    Sflm::flm('css')->addLib('i/css/common/ddTagsMultiselectDropdown.css');
    return parent::typeJs();
  }
}
