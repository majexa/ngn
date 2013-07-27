<?php

class FieldEProcent extends FieldENum {

  protected function defineOptions() {
    $this->options['cssClass'] = 'validate-procent';
    $this->options['help'] = '%';
  }

}