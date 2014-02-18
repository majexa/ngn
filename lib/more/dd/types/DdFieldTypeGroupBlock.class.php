<?php

class DdFieldTypeGroupBlock extends DdFieldType {

  static protected function _get() {
    return [
      'title'        => 'Блок для группировки',
      'order'        => 160,
      'virtual'      => true,
      'system'       => true,
      'noElementTag' => true
    ];
  }

}