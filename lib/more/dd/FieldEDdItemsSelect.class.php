<?php

DdFieldCore::registerType('ddItemsSelect', [
  'dbType'   => 'INT',
  'dbLength' => 11,
  'title'    => 'Выбор dd-записей',
  'order'    => 300,
  'tags'     => true,
  'ddItems'  => true,
  'fields'   => [
    [
      'type'  => 'ddStructure',
      'title' => 'Структура',
      'name'  => 'strName'
    ]
  ]
]);

class FieldEDdItemsSelect extends FieldEMultiselect {
use DdElement;

  static $multi = true;

  protected function init() {
    if (isset($this->options['settings'])) {
      $this->options['options'] = Arr::get(O::get('DdItems', $this->options['settings']['strName'])->getItemsSimple(), 'title', 'id');
    } else {
      $this->options['options'] = [];
    }
    parent::init();
  }

}