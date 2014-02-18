<?php

class DdFieldCore {

  static function isGroup($type) {
    return FieldCore::hasAncestor($type, 'headerAbstract');
  }

  static function getIconPath($type) {
    return file_exists(NGN_PATH.'/i/img/icons/fields/'.$type.'.gif') ? './i/img/icons/fields/'.$type.'.gif' : './i/img/blank.gif';
  }

  static function getFieldsFromTable($strName) {
    return Arr::get(db()->select("SHOW COLUMNS FROM dd_i_$strName"), 'Fields');
  }

  static function isNumberType($type) {
    return FieldCore::hasAncestor($type, 'num') or FieldCore::hasAncestor($type, 'float');
  }

  static function isFileType($type) {
    return FieldCore::hasAncestor($type, 'file');
  }

  static function isBoolType($type) {
    return FieldCore::hasAncestor($type, 'bool') or FieldCore::hasAncestor($type, 'boolCheckbox');
  }

  static function isRangeType($type) {
    return in_array($type, ['numberRange']);
  }

  static function isFormatType($type) {
    return in_array($type, ['textarea']);
  }

  static protected $types = [];

  static function getTypeData($type, $strict = true) {
    $class = 'DdFieldType'.ucfirst($type);
    /* @var $class DdFieldType */
    if (!class_exists($class)/* or !is_subclass_of($class, 'DdFieldType')*/) {
      if ($strict) throw new EmptyException("Class '$class' does not exists");
      else
        return false;
    }
    return $class::get();
  }

  static function typeExists($type) {
    return class_exists('DdFieldType'.ucfirst($type));
  }

  /**
   * Возвращает данные типов динамических полей
   * @return array
   */
  static function getTypes() {
    $classes = ClassCore::getClassesByPrefix('DdFieldType');
    $types = [];
    foreach ($classes as $class) {
      /* @var $class DdFieldType */
      if ((new ReflectionClass($class))->isAbstract()) continue;
      $types[ClassCore::classToName('DdFieldType', $class)] = $class::get();
    }
    return Arr::sortByOrderKey($types, 'order');
  }

}
