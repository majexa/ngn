<?php

class FieldEDdItemsSelect extends FieldEMultiselect {
use DdElement;

  static $ddTags = true, $ddTagsMulti = true, $ddTagItems = true;

  protected function init() {
    if (isset($this->options['settings'])) {
      $this->options['options'] = Arr::get(O::get('DdDbItemsExtended', $this->options['settings']['strName'])->getItemsSimple(), 'title', 'id');
    } else {
      $this->options['options'] = [];
    }
    parent::init();
  }

}