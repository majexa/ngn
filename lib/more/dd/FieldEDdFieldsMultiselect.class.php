<?php

DdFieldCore::registerType('ddFieldsMultiselect', [
  'dbType' => 'TEXT',
  'title'  => 'Выбор нескольких dd-полей',
  'order'  => 300,
  'fields' => [
    [
      'type'  => 'ddStructure',
      'title' => 'Структура',
      'name'  => 'strName'
    ],
    [
      'type'  => 'bool',
      'title' => 'показывать системные',
      'name'  => 'getSystem'
    ],
    [
      'type'  => 'bool',
      'title' => 'показывать неразрешенные',
      'name'  => 'getDisallowed'
    ],
  ]
]);

class FieldEDdFieldsMultiselect extends FieldEMultiselect {

  protected function init() {
    $this->options['options'] = Arr::get(O::get('DdFields', $this->options['settings']['strName'], [
      'getSystem'     => !empty($this->options['settings']['getSystem']),
      'getDisallowed' => !empty($this->options['settings']['getDisallowed'])
    ])->getFields(), 'title', 'name');
    if (count($this->options['options']) > 10) $this->options['rowClass'] = 'longMultiselect';
    parent::init();
  }

}