<?php

class FieldEFieldList extends FieldEFieldSetAbstract {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'deleteTitle'  => 'Удалить поле',
      'cleanupTitle' => 'Очистить поле'
    ]);
  }

  protected function init() {
    $this->options['fields'] = [
      [
        'name' => 'dummy',
        'type' => empty($this->options['fieldsType']) ? 'text' : $this->options['fieldsType']
      ]
    ];
    if (isset($this->options['placeholder'])) {
      $this->options['fields'][0]['placeholder'] = $this->options['placeholder'];
    }
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