<?php

DdFieldCore::registerType('ddMetroMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Выбор нескольких станций метро',
  'order'    => 251,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdMetroMultiselect extends FieldEDdTagsTreeMultiselectAc {
}