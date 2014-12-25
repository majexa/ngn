<?php

class FieldEImagedRadio extends FieldERadio {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
    ]);
  }

  public $markerHtml = '<div class="marker"><i></i></div>';

}
