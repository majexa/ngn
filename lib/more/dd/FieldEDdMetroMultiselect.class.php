<?php

DdFieldCore::registerType('ddMetroMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Метро мультибыбор',
  'order'    => 301,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdMetroMultiselect extends FieldEDdTagsTreeMultiselectDialogable {
}