<?php

class DdStructureCore {

  static function getTypes() {
    return [
      'dynamic' => 'Динамическая',
      'static' => 'Статическая',
      'variant' => 'Любая'
    ];
  }
  
  static function getDefaultFields($type) {
    if ($type != 'static' and $type != 'variant') return [];
    return [[
      'name' => 'static_id',
      'title' => 'static_id',
      'type' => 'num',
      'system' => 1, 
      'editable' => 0,
      'virtual' => 1,
      'notList' => 1 
    ]];
  }

  static function create($name, array $fields) {
    (new DdStructuresManager)->create([
      'title' => $name,
      'name' => $name
    ]);
    $fieldsManager = new DdFieldsManager($name);
    foreach ($fields as $field) $fieldsManager->create($field);
  }

  static function copy($name, $newName) {
  }

}