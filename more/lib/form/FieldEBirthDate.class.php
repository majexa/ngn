<?php

class FieldEBirthDate extends FieldEDate {

  protected function getLastYear() {
    return date('Y') - 18;
  }

  protected function validate1() {
    if (empty($this->options['value'][0])) {
      $this->error = "Field «{$this->options['title']}» is required";
    }
    if (empty($this->options['value'][1])) {
      $this->error = "Field «{$this->options['title']}» is required";
    }
    if (empty($this->options['value'][2])) {
      $this->error = "Field «{$this->options['title']}» is required";
    }
  }

}