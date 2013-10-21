<?php

DdFieldCore::registerType('ddTags', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Тэги (через запятую)',
  'order' => 210,
  'tags' => true,
]);

class FieldEDdTagsFieldList extends FieldEFieldList {
}