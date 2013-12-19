<?php

DdFieldCore::registerType('ddCityMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Город мультивыбор',
  'order'    => 291,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdCityMultiselect extends FieldEDdTagsTreeMultiselectAc {
}