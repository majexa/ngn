<?php

class FieldEDdOrder extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = DdFieldOptions::order($this->form->strName);
  }

}