<?php

class FieldEDdItemSelect extends FieldESelect {

  static $ddTags = true, $ddTagItems = true;

  protected function init() {
    $this->options['options'] = ['' => '—'] + Arr::get(O::get('DdDbItemsExtended', $this->options['settings']['strName'])->getItemsSimple(), 'title', 'id');
    if (!empty($this->options['ddFilter'])) $this->options['options'] = Arr::filterByKeys($this->options['options'], $this->options['ddFilter']);
    parent::init();
  }

}