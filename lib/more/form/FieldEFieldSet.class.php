<?php

class FieldEFieldSet extends FieldEFieldSetAbstract {

  static $requiredOptions = ['name', 'fields'];
  
  protected function getName($n, $name) {
    return $this->options['name']."[$n][$name]";
  }

}