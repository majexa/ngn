<?php

class FieldEDdTagsMultiselect extends FieldEMultiselect {

  static $ddTags = true, $ddTagsMulti = true;

  protected function init() {
    $this->options['options'] = Arr::get(DdTags::get($this->form->strName, $this->options['name'])->getTags(), 'title', 'id');
    if(!empty($this->options['ddFilter']))$this->options['options'] = Arr::filterByKeys($this->options['options'], $this->options['ddFilter']);
    parent::init();
  }

  function titledValue() {
    $value = $this->value();
    if (is_array($value)) {
      $r = [];
      foreach ($value as $v) $r[] = $this->options['options'][$v];
      return Tt()->enum($r);
    }
    else {
      return isset($this->options['options'][$value]) ? $this->options['options'][$value] : '';
    }
  }

}
