<?php

class DdFieldTypeUrls extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'Несколько ссылок',
      'order'  => 180
    ];
  }

}