<?php

DdFieldCore::registerType('ddCityMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Город мультивыбор',
  'order'    => 291,
]);

class FieldEDdCityMultiselect extends FieldEDdTagsTreeMultiselectAc {
}