<?php

DdFieldCore::registerType('ddTags', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Тэги (список)',
  'order' => 210,
  'tags' => true,
]);

class FieldEDdTagsFieldList extends FieldEFieldList {
}