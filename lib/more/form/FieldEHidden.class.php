<?php

class FieldEHidden extends FieldEText {

  public $inputType = 'hidden';

  protected function defineOptions() {
    return [
      'type' => 'hidden',
      'noRowHtml' => true
    ];
  }

}
