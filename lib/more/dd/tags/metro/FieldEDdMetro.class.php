<?php

DdFieldCore::registerType('ddMetro', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Метро',
  'order'    => 300,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdMetro extends FieldEDdTagsTreeMultiselect {
}


