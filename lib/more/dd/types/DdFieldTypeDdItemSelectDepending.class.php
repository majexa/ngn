<?php

class DdFieldTypeDdItemSelectDepending extends DdFieldTypeDdItemSelect {

  static protected function _get() {
    $r = parent::_get();
    $r['title'] = 'Выбор dd-записи (зависимый)';
    $r['fields'][] =         [
      'type'  => 'ddStructure',
      'title' => 'Родитель, от которого зависим',
      'name'  => 'parentStrName'
    ];
    return $r;
  }

}