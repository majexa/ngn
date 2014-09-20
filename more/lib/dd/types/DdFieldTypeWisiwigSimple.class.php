<?php

class DdFieldTypeWisiwigSimple extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Текстовое поле с базовым визуальным редактором',
      'order'  => 111
    ];
  }

  function sampleData() {
    return TestCore::largeTextFixture();
  }

}