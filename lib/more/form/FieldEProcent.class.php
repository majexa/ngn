<?php

class FieldEProcent extends FieldENum {

  protected function defineOptions() {
    return [
      'cssClass' => 'validate-procent',
      'help' => '%'
    ];
  }

}