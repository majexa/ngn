<?php

class DdSyncFileToDb {

  function __construct() {
    $dbStructures = Arr::assoc(db()->select('SELECT * FROM dd_fields'), 'strName', true);
    $fileStructures = require PROJECT_PATH.'/structures.php';
    $structuresManager = new DdStructuresManager;
    //
    foreach ($fileStructures as $strName => $fileStructure) {
      $fieldsManager = new DdFieldsManager($strName);
      if (!isset($dbStructures[$strName])) {
        // создание структуры
        output("creating structure $strName");
        $structuresManager->create([
          'title' => $strName,
          'name' => $strName
        ]);
      }
      $dbFields = db()->select('SELECT * FROM dd_fields WHERE strName=?', $strName);
      foreach ($fileStructure as $fileField) {
        $dbField = Arr::getValueByKey($dbFields, 'name', $fileField['name']);
        if (!$dbField) {
          // Если поля нет в БД, создаём его
          output('creating '.$strName.'::'.$fileField['name']);
          $fieldsManager->create($fileField);
        } else {
          $fieldId = $dbField['id'];
          $dbField = Arr::filterByKeys($dbField, ['title', 'name', 'type', 'required', 'system']);
          $dbField = Arr::filterEmpties($dbField);
          // Если есть, проверяем его идентичность и апдейтим, если отличается
          if (array_diff($fileField, $dbField)) {
            output('updating '.$strName.'::'.$fileField['name']);
            $fieldsManager->update($fieldId, $fileField);
          }
        }
      }
    }
  }

}