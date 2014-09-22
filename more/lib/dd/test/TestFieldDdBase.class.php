<?php

class TestFieldDdBase extends TestDd {

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    $fieldType = lcfirst( //
      Misc::removePrefix('TestUiField', //
        Misc::removePrefix('TestField', //
          get_called_class())));
    O::di('DdFieldsManager', static::$strName)->create(static::fieldData($fieldType));
  }

  static protected function fieldData($fieldType) {
    return [
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => $fieldType
    ];
  }

}