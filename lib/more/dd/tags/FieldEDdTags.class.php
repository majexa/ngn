<?php

DdFieldCore::registerType('ddTags', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Тэги (через запятую)',
  'order' => 210,
]);

class FieldEDdTags extends FieldEText {

  static $ddTags = true, $ddTagsItemsDirected = true, $ddTagsMulti = true;

  protected $useTypeJs = true;

}