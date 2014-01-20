<?php

DdFieldCore::registerType('ddItemSelect', [
  'dbType' => 'INT',
  'dbLength' => 11,
  'title' => 'Выбор dd-записи',
  'order' => 300,
  'fields' => [
    [
      'type' => 'ddStructure',
      'title' => 'Структура',
      'name' => 'strName'
    ]
  ]
]);

class FieldEDdItemSelect extends FieldESelect {

  static $ddTags = true, $ddTagItems = true;

  protected function init() {
    $this->options['options'] = ['' => '—'] + Arr::get(O::get('DdItems', $this->options['settings']['strName'])->getItemsSimple(), 'title', 'id');
    parent::init();
  }

}