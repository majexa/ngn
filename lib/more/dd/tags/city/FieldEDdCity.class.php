<?php

DdFieldCore::registerType('ddCity', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Город',
  'order' => 290,
  'tags' => true,
  'tagsTree' => true
]);

class FieldEDdCity extends FieldEDdTagsConsecutiveSelect {}