<?php

abstract class DdFieldTypeSelectTagsStructure extends DdFieldType {

  static protected function fields() {
    return [
      [
        'type'  => 'ddStructure',
        'title' => 'Структура из которой брать тэги',
        'name'  => 'tagsStrName'
      ]
    ];
  }

}