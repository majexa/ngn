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
   * Создаёт структуру или изменяет существующую структуру, включая поля.
   * Возвращает true, если были произведены изменения или false, если не были.
   *
   * @param string $strName
   * @param array $strFields
   * @param bool $strict
   * @return bool
   * @throws EmptyException|AlreadyExistsException
   * @throws Exception
   */
  static function create($strName, array $strFields, $strict = true) {
    $updated = false;
    try {
      (new DdStructuresManager)->create([
        'title' => $strName,
        'name' => $strName
      ]);
      output2("Structure '$strName' created");
      $updated = true;
    } catch (AlreadyExistsException $e) {
      if ($strict) throw $e;
    }
    $fieldsManager = new DdFieldsManager($strName);
    foreach ((new DdFields($strName))->getFieldsF() as $existingField) {
      if (!Arr::subValueExists($strFields, 'name', $existingField['name'])) {
        $fieldsManager->deleteByName($existingField['name']);
        output($existingField['name'].' removed');
      }
    }
    foreach ($strFields as $strField) {
      if (($existingField = $fieldsManager->items->getItemByField('name', $strField['name']))) {
        $strFieldKeys = array_keys($strField);
        $existingFieldFiltered = Arr::filterByKeys($existingField, $strFieldKeys);
        if ($existingFieldFiltered != $strField) {
          $updated = true;
          output("Updating '{$existingField['name']}' field. (str: $strName)");
          $fieldsManager->update($existingField['id'], $strField);
        }
      } else {
        $updated = true;
        output("Creating '{$strField['name']}' field. (str: $strName)");
        $fieldsManager->create($strField);
      }
    }
    return $updated;
  }

  /**
   * @api
   * Создаёт структуры из файлов structures.php найденных в базовых ngn-директориях
   */
  static function install() {
    throw new Exception(__CLASS__.'::install() is not yet implemented');
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

  /**
   * @api
   * Сохраняет структуру в SQL-файл PROJECT_PATH/strName.sql
   *
   * @param $strName
   * @throws Exception
   */
  static function export($strName) {
    $file = PROJECT_PATH."/$strName.sql";
    file_put_contents($file, '');
    $dumper = new DbDumper;
    file_put_contents($file, "\n# Cleanup structure fields\n\nDELETE FROM dd_fields WHERE strName='$strName';\n", FILE_APPEND);
    $dumper->cond->addF('strName', $strName);
    $dumper->dataDump('dd_fields', $file);
    $dumper = new DbDumper;
    file_put_contents($file, "\n# Remove structure\n\nDELETE FROM dd_structures WHERE name='$strName';\n", FILE_APPEND);
    $dumper->cond->addF('name', $strName);
    $dumper->dataDump('dd_structures', $file);
    $dumper = new DbDumper;
    $dumper->structureDump('dd_i_'.$strName, $file);
    $dumper->dataDump('dd_i_'.$strName, $file);
  }

  /**
   * @api
   * Импортирует структуру из SQL-файла PROJECT_PATH/strName.sql
   *
   * @param $strName
   * @throws Exception
   */
  static function import($strName) {
    db()->importFile(PROJECT_PATH."/$strName.sql");
  }

}
