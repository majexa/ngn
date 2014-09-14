<?php

class DdFieldTypeImagePreview extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Изображение',
      'order'    => 50
    ];
  }

  function sampleData() {
    return TestRunnerNgn::tempImageFixture();
  }


}