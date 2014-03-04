<?php

class FieldEDdTagsSelect extends FieldESelect {
  use DdElement;

  static $ddTags = true;

  protected function init() {
    $opts = Arr::get(DdTags::get($this->strName, $this->options['name'])->getTags(), 'title', 'id');
    $this->options['options'] = $this->options['required'] ? $opts : ['' => '—'] + $opts;
    if (!empty($this->options['ddFilter'])) $this->options['options'] = ['' => '—'] + Arr::filterByKeys($this->options['options'], $this->options['ddFilter']);
    parent::init();
  }

  protected function prepareValue() {
    if (empty($this->options['value']) and !empty($this->options['default'])) $this->defaultCaption = $this->options['default'];
  }

}