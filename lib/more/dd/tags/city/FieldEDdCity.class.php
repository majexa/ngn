<?php

DdFieldCore::registerType('ddCity', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Город',
  'order' => 290,
]);

class FieldEDdCity extends FieldEDdTagsConsecutiveSelect {}