<?php

class DdFieldTypeBool extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'int',
      'dbLength' => 1,
      'title'    => 'Да / нет (радио)',
      'order'    => 30
    ];
  }

}