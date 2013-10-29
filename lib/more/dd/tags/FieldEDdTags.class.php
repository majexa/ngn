<?php

DdFieldCore::registerType('ddTags', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Тэги (через запятую)',
  'order' => 210,
  'tags' => true,
  'tagsItemsDirected' => true
]);

/*
DdFieldCore::registerType('ddTagsFieldSet', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Тэги филдсет',
  'order' => 210,
  'tags' => true
]);
*/

class FieldEDdTags extends FieldEText {
  
  protected $useTypeJs = true;
  
}