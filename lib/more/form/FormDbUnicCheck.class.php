<?php

trait FormDbUnicCheck {

  abstract protected function unicCheckCond();

  protected function unicCheck($name, $errorText, DbCond $cond = null, $tableField = null) {
    if (!($el = $this->getElement($name))) return;
    if (!$this->create and !$el->valueChanged) return;
    if (!$tableField) $tableField = $name;
    if (!$cond) {
      $cond = $this->unicCheckCond();
      if (is_string($cond)) $cond = new DbCond($cond);
    }
    $cond->addF($tableField, $el->value());
    if (db()->selectCell("SELECT * FROM {$cond->table}".$cond->all())) $el->error($errorText);
  }

}
