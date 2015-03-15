<?php

class DdFieldTypeTypoText extends DdFieldType {

  protected function _get() {
    return [
      'dbType'   => 'VARCHAR',
      'dbLength' => 255,
      'title'    => 'Одностройчное поле с типографированием',
      'order'    => 20
    ];
  }

  function sampleData() {
    return 'Пример - текста "с типографированием"';
  }

}