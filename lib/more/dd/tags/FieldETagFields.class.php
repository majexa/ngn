<?php

class FieldETagFields extends FieldESelect {
use DdElement;

  protected function defineOptions() {
    $this->options['options'] = ['' => '— '.LANG_NOTHING_SELECTED.' —'];
    foreach (O::get('DdFields', $this->oForm->strName)->getTagFields() as $v) $this->options['options'][$v['name']] = $v['title'];
  }

}
