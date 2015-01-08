<?php

class DdFieldsForm extends DdForm {

  /**
   * @var FormDbUnicCheck
   */
  protected $uc;

  protected function init() {
    $this->uc = new FormDbUnicCheck((new DbCond('dd_fields'))->addF('strName', $this->strName), $this);
  }

  protected function initErrors() {
    $this->uc->check('name', 'Поле с таким именем уже существует');
  }

}