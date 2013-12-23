<?php

DdFieldCore::registerType('ddTagsSelect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Выбор одного тэга',
  'order'    => 220,
]);

class FieldEDdTagsSelect extends FieldESelect {
use DdElement;

  static $ddTags = true;

  protected function init() {
    $opts = Arr::get(DdTags::get($this->strName, $this->options['name'])->getTags(), 'title', 'id');
    $this->options['options'] = $this->options['required'] ? $opts : ['' => '—'] + $opts;
    prr([$this->options['value'], $this->options['options']]);
    parent::init();
  }

  protected function prepareValue() {
    if (empty($this->options['value']) and !empty($this->options['default'])) $this->defaultCaption = $this->options['default'];
  }

}