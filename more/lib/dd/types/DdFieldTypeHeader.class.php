<?php

class DdFieldTypeHeader extends DdFieldType {

  static protected function _get() {
    return [
      'title'   => 'Заголовок',
      'order'   => 160,
      'virtual' => true,
    ];
  }

}