<?php

class DdFieldTypeHeaderToggle extends DdFieldType {

  static protected function _get() {
    return [
      'title'   => 'Заголовок-переключатель',
      'order'   => 160,
      'virtual' => true,
    ];
  }

}