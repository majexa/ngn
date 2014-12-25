<?php

class FieldEDateRange extends FieldEText {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
    ]);
  }

}