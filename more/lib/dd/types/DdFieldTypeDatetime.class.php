<?php

class DdFieldTypeDatetime extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'DATETIME',
      'title'  => 'Дата, время',
      'order'  => 90
    ];
  }

  function sampleData() {
    return explode('.', date('d.m.Y.H.i'));
  }

}