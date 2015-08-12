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

  /**
   * @api
   * Создаёт структуру
   *
   * @param $name
   * @param array $fields
   * @return bool|int
   * @throws AlreadyExistsException
   * @throws EmptyException
   * @throws Exception
   */
  static function create($name, array $fields) {
    $id = (new DdStructuresManager)->create([
      'title' => $name,
      'name' => $name
    ]);
    $fieldsManager = new DdFieldsManager($name);
    foreach ($fields as $field) $fieldsManager->create($field);
    return $id;
  }

  /**
   * @api
   * Переименовывает структуру
   *
   * @param string $name Текущее имя
   * @param string $newName Новое имя
   * @throws EmptyException
   * @throws Exception
   */
  static function rename($name, $newName) {
    $manager = new DdStructuresManager;
    $manager->update($manager->items->getItemByField('name', $name)['id'], [
      'name' => $newName
    ]);
  }

}