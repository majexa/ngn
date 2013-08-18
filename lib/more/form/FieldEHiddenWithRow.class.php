<?php

class FieldEHiddenWithRow extends FieldEHidden {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['noRowHtml' => false]);
  }
  
}