<?php

class FieldEDdFields extends FieldESelect {

  protected function defineOptions() {
    return ['options' => DdFieldOptions::fields($this->form->strName)];
  }

}