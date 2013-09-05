<?php

DdFieldCore::registerType('ddCityList', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Список городов',
  'order' => 210,
  'tags' => true
]);

class FieldEDdCityList extends FieldEFieldList {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [ 'fieldsType' => 'ddCity']);
  }

}