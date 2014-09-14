<?php

class DdFieldTypeFloatBlock extends DdFieldType {

  protected function _get() {
    return [
      'title'        => 'Блок для обтекания',
      'order'        => 160,
      'virtual'      => true,
      'system'       => true,
      'noElementTag' => true
    ];
  }

}