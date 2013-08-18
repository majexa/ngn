<?php

DdFieldCore::registerType('fieldList', [
  'dbType' => 'TEXT',
  'title'  => 'Набор текстовых полей',
  'order'  => 110
]);

class FieldEFieldList extends FieldEFieldSetAbstract {

  protected function defineOptions() {
    return [
      'deleteTitle' => 'Удалить поле',
      'cleanupTitle' => 'Очистить поле'
    ];
  }

  protected function init() {
    $this->options['fields'] = [
      [
        'name' => 'dummy',
        'type' => empty($this->options['fieldsType']) ? 'text' : $this->options['fieldsType']
      ]
    ];
    parent::init();
  }

  protected function getName($n, $name) {
    return $this->options['name']."[$n]";
  }

  protected function addFieldData(array $v) {
    $v['required'] = !empty($this->options['required']);
    return $v;
  }

}