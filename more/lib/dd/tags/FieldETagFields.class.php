<?php

class FieldETagFields extends FieldESelect {
use DdElement;

  protected function defineOptions() {
    $options['options'] = ['' => '— '.Lang::get('nothingSelected').' —'];
    foreach (O::get('DdFields', $this->form->strName)->getTagFields() as $v) $options['options'][$v['name']] = $v['title'];
    return $options;
  }

}
