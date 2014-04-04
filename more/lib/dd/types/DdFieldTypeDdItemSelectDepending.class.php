<?php

class DdFieldTypeDdItemSelectDepending extends DdFieldTypeDdItemSelect {

  static protected function _get() {
    $r = parent::_get();
    $r['title'] = 'Выбор dd-записи (зависимый)';
    $r['fields']['parent'] =         [
      'type'  => 'ddStructure',
      'title' => 'Родитель, от которого зависим',
      'name'  => 'parentStrName',
    ];
    $r['fields'][] =         [
      'type'  => 'text',
      'title' => 'Поле родителя',
      'name'  => 'parentTagFieldName',
    ];
    return $r;
  }

}