<?php

class DdFieldTypeImagesPreview extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Изображения',
      'order'    => 51
    ];
  }

  function sampleData() {
    return TestCore::tempImageFixture();
  }


}