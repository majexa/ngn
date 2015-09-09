<?php

class DdFieldsForm extends DdForm {

  protected function initErrors() {
    $el = $this->getElement('name');
    if ((new DdFields($this->strName))->exists($el->value())) $el->error('Поле с таким именем уже существует');
  }

}