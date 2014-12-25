<?php

class FieldEProcent extends FieldENum {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'cssClass' => 'validate-procent',
      'help' => '%'
    ]);
  }

}