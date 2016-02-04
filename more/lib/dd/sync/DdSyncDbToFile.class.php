<?php

class DdSyncDbToFile {

  function __construct() {
    $r = [];
    foreach ((new DdStructureItems)->getItems() as $structure) {
      $r[$structure['name']] = [];
      foreach (db()->select('SELECT * FROM dd_fields WHERE strName=?', $structure['name']) as $dbField) {
        $dbField = Arr::filterByKeys($dbField, ['title', 'name', 'type', 'required', 'system']);
        $dbField = Arr::filterEmpties($dbField);
        foreach ($dbField as &$v) {
          if (is_numeric($v)) $v = (int)$v;
        }
        $r[$structure['name']][] = $dbField;
      }
    }
    FileVar::updateVar(PROJECT_PATH.'/structures.php', $r);
  }

}
