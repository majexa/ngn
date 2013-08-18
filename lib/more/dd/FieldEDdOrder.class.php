<?php

class FieldEDdOrder extends FieldESelect {

  protected function defineOptions() {
    return ['options' => DdFieldOptions::order($this->form->strName)];
  }

}