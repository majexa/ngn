<?php

class DdFieldsForm extends DdForm {
use FormDbUnicCheck;

  protected function unicCheckCond() {
    return (new DbCond('dd_fields'))->addF('strName', $this->strName);
  }

  protected function initErrors() {
    $this->unicCheck('name', 'Поле с таким именем уже существует', $this->unicCheckCond());
  }

}