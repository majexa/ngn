<?php

abstract class DdFieldType extends ArrayAccesseble {

  function __construct() {
    $this->r = $this->get();
  }

  protected function get() {
    $data = $this->_get();
    Arr::checkEmpty($data, ['title', 'order']);
    if (empty($data['virtual'])) {
      Arr::checkEmpty($data, 'dbType');
      if (!preg_match('/(.*TEXT|DATE|TIME|DATETIME)/', $data['dbType'])) Arr::checkEmpty($data, 'dbLength');
      if (empty($data['dbLength'])) $data['dbLength'] = null;
    }
    $data['type'] = ClassCore::classToName('DdFieldType', get_called_class());
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