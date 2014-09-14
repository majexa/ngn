<?php

class DdFieldTypeEmails extends DdFieldType {

  protected function _get() {
    return [
      'dbType' => 'TEXT',
      'title'  => 'E-mail`ы',
      'order'  => 60
    ];
  }

}