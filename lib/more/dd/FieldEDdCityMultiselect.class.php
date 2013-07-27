<?php

DdFieldCore::registerType('ddCityMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Выбор нескольких городов',
  'order'    => 251,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdCityMultiselect extends FieldEDdTagsTreeMultiselect {
}