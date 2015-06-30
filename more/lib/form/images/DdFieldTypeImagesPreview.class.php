<?php

class DdFieldTypeImagesPreview extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'TEXT',
      'title'    => 'Изображения',
      'order'    => 51
    ];
  }

  function sampleData() {
    return TestCore::tempImageFixture();
  }


}