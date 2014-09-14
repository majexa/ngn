<?php

class DdFieldTypeHeaderClose extends DdFieldType {

  protected function _get() {
    return [
      'title'   => 'Заголовок конец',
      'order'   => 160,
      'virtual' => true,
    ];
  }

}