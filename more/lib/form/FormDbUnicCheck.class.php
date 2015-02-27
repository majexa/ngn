<?php

class FormDbUnicCheck {

  protected $cond, $form;

  function __construct(DbCond $cond, Form $form) {
    $this->cond = $cond;
    $this->form = $form;
  }

  function check($name, $errorText, $tableField = null) {
    if (!($el = $this->form->getElement($name))) return;
    if (!$this->form->create and !$el->valueChanged) return;
    if (!$tableField) $tableField = $name;
    $this->cond->addF($tableField, $el->value());
    if (db()->selectCell("SELECT * FROM {$this->cond->table}".$this->cond->all())) $el->error($errorText);
  }

}
