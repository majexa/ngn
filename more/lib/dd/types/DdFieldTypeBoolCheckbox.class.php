<?php

class DdFieldTypeBoolCheckbox extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'int',
      'dbLength' => 1,
      'title'    => 'Да / нет (чекбокс)',
      'order'    => 20
    ];
  }

}