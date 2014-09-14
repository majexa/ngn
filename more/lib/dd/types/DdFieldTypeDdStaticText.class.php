<?php

class DdFieldTypeDdStaticText extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Статический текст в форме',
      'virtual'  => true,
      'order'    => 150
    ];
  }

}