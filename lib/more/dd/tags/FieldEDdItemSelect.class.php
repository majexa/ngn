<?php

class FieldEDdItemSelect extends FieldESelect {

  static $ddTags = true, $ddTagItems = true;

  protected function init() {
    $this->options['options'] = ['' => '—'] + Arr::get(O::get('DdItems', $this->options['settings']['strName'])->getItemsSimple(), 'title', 'id');
    parent::init();
  }

}