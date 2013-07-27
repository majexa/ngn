<?php

class FieldEFullName extends FieldEText {

  protected function defineOptions() {
    $this->options['cssClass'] = 'validate-fullName';
  }

  protected function vlidate2() {
    //if (!preg_match('/^\S+\s+\S+\s+\S+$/', $this->options['value']))
      //$this->error = "Неправильный формат имени";
  }

  protected function prepareValue() {
    parent::prepareValue();
    $this->options['value'] = trim($this->options['value']);
    $this->options['value'] = preg_replace('/\s+/', ' ', $this->options['value']);
    $this->options['value'] = implode(' ', array_map(['Misc', 'ucfirst'], explode(' ', $this->options['value'])));
  }

}