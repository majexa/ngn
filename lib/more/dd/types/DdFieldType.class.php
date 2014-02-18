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
    $r['type'] = ClassCore::classToName('FieldE', get_called_class());
    if (empty($r['dbLength'])) $r['dbLength'] = null;
    return $data;
  }

  abstract static protected function _get();

}