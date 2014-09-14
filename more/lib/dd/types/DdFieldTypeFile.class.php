<?php

class DdFieldTypeFile extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Файл',
      'order'    => 40
    ];
  }

  function sampleData() {
    return TestRunnerNgn::tempImageFixture();
  }

}