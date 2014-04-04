<?php

abstract class DdFieldType {

  static function get() {
    $data = static::_get();
    Arr::checkEmpty($data, ['title', 'order']);
    if (!empty($data['virtual'])) $data = array_merge($data, [
      'dbType'   => 'INT',
      'dbLength' => 1
    ]);
    Arr::checkEmpty($data, 'dbType');
    if (!preg_match('/(.*TEXT|DATE|TIME|DATETIME)/', $data['dbType'])) Arr::checkEmpty($data, 'dbLength');
    $data['type'] = ClassCore::classToName('DdFieldType', get_called_class());
    if (empty($data['dbLength'])) $data['dbLength'] = null;
    if (($fields = static::fields())) $data['fields'] = $fields;
    return $data;
  }

  abstract static protected function _get();

  static protected function fields() {
    return false;
  }

}