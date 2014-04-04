<?php

class FieldEDdDateFields extends FieldESelect {

  protected function defineOptions() {
    return ['options' => DdFieldOptions::date($this->form->strName)];
  }

}