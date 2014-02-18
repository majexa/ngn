<?php

class FieldEDdFieldsMultiselect extends FieldEMultiselect {

  protected function init() {
    $this->options['options'] = Arr::get(O::get('DdFields', $this->options['settings']['strName'], [
      'getSystem'     => !empty($this->options['settings']['getSystem']),
      'getDisallowed' => !empty($this->options['settings']['getDisallowed'])
    ])->getFields(), 'title', 'name');
    if (count($this->options['options']) > 10) $this->options['rowClass'] = 'longMultiselect';
    parent::init();
  }

}