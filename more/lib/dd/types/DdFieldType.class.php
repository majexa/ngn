<?php

abstract class DdFieldType extends ArrayAccesseble {

  function __construct() {
    $this->r = $this->get();
  }

  protected function get() {
    $data = $this->_get();
    Arr::checkEmpty($data, ['title', 'order']);
    if (!empty($data['virtual'])) $data = array_merge($data, [
      'dbType'   => 'INT',
      'dbLength' => 1
    ]);
    Arr::checkEmpty($data, 'dbType');
    if (!preg_match('/(.*TEXT|DATE|TIME|DATETIME)/', $data['dbType'])) Arr::checkEmpty($data, 'dbLength');
    $data['type'] = ClassCore::classToName('DdFieldType', get_called_class());
    if (empty($data['dbLength'])) $data['dbLength'] = null;
    if (($fields = $this->fields())) $data['fields'] = $fields;
    return $data;
  }

  abstract protected function _get();

  protected function fields() {
    return false;
  }

  function sampleData() {
    return null;
  }

}