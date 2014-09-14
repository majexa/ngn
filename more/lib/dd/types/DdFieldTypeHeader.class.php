<?php

class DdFieldTypeHeader extends DdFieldType {

  protected function _get() {
    return [
      'title'   => 'Заголовок',
      'order'   => 160,
      'virtual' => true,
    ];
  }

}