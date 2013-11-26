<?php

DdFieldCore::registerType('ddTagsMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Выбор нескольких тэгов',
  'order'    => 230,
  'tags'     => true
]);

class FieldEDdTagsMultiselect extends FieldEMultiselect {

  protected function init() {
    $this->options['options'] = Arr::get(DdTags::get($this->form->strName, $this->options['name'])->getTags(), 'title', 'id');
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
