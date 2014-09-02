<?php

class FieldEInvalid extends FieldEText {

  protected function validate2() {
    if ($this->options['value'] != 'valid') {
      $this->error = 'invalid';
    }
  }

}