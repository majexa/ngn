<?php

DdFieldCore::registerType('ddItemSelect', [
  'dbType' => 'INT',
  'dbLength' => 11,
  'title' => 'Выбор dd-записи',
  'order' => 300,
  'tags' => true,
  'ddItems'  => true,
  'fields' => [
    [
      'type' => 'ddStructure',
      'title' => 'Структура',
      'name' => 'strName'
    ]
  ]
]);

class FieldEDdItemSelect extends FieldESelect {

  protected function init() {
    $this->options['options'] = ['' => '—'] + Arr::get(O::get('DdItems', $this->options['settings']['strName'])->getItemsSimple(), 'id', 'id');
    parent::init();
  }

}