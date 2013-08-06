<?php

class FieldEDdFields extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = DdFieldOptions::fields($this->form->strName);
  }

}