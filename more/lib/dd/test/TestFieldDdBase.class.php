<?php

class TestFieldDdBase extends TestDd {

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    $fieldType = lcfirst( //
      Misc::removePrefix('TestUiField', //
        Misc::removePrefix('TestField', //
          get_called_class())));
    O::di('DdFieldsManager', static::$strName)->create(static::fieldData($fieldType));
    O::di('DdFieldsManager', static::$strName)->create([
      'name'  => 'sample2',
      'title' => 'sample2',
      'type'  => 'file'
    ]);

  }

  static protected function fieldData($fieldType) {
    return [
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => $fieldType
    ];
  }

}