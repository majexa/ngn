<?php

class DdFieldTypeDdItemSelectDepending extends DdFieldTypeDdItemSelect {

  static protected function _get() {
    $r = parent::_get();
    $r['title'] = 'Выбор dd-записи (зависимый)';
    $r['fields'][] = [
      'title' => 'asd',
      'type'  => 'text'
    ];
    return $r;
  }

}