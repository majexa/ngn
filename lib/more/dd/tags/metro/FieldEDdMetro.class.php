<?php

DdFieldCore::registerType('ddMetro', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Метро',
  'order' => 300,
  'tags' => true,
  'tagsTree' => true
]);

class FieldEDdMetro extends FieldEDdTagsConsecutiveSelect {

  protected function getRootOptions() {
    if (!empty($this->options['rootTagId'])) {
      $r = array_merge(['' => ' — ', '0' => ''], parent::getRootOptions());
      unset($r[0]);
      return $r;
    }
    else return parent::getRootOptions();
  }

}