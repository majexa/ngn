<?php

class DdFieldTypeEmails extends DdFieldType {

  static protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'E-mail`ы',
      'order'  => 60
    ];
  }

}