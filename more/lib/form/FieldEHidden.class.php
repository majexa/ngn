<?php

class FieldEHidden extends FieldEText {

  public $inputType = 'hidden';

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'type' => 'hidden',
      'noRowHtml' => true
    ]);
  }

}
