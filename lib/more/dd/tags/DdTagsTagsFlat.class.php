<?php

class DdTagsTagsFlat extends DdTagsTagsBase {

  function getByTitle($title) {
    $cond = clone $this->getSelectCond();
    $q = 'SELECT * FROM '.$this->group->table.$cond->addF('title', $title)->all();
    $r = db()->selectRow($q);
    return $r;
  }

  function delete($id) {
    parent::delete($id);
    foreach (db()->ids($this->group->table, DbCond::get()->addF('parentId', $id)) as $childId) $this->delete($childId);
  }

  protected $importSeparator = ',';

  function setImportSeparator($s) {
    $this->importSeparator = $s;
  }

  function import($text) {
    foreach (explode($this->importSeparator, $text) as $v) $titles[] = trim($v);
    for ($i = 0; $i < count($titles); $i++) $this->create([
      'title' => $titles[$i],
      'oid'   => ($i + 1) * 10
    ]);
  }

  function getData() {
    return $this->getTags();
  }

  function getTags() {
    return db()->query("SELECT *, id AS ARRAY_KEY, 0 AS cnt FROM {$this->group->table}".$this->getSelectCond()->all());
  }


}