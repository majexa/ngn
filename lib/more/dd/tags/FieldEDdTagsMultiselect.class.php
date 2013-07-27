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
    $this->options['options'] = Arr::get(DdTags::get($this->oForm->strName, $this->options['name'])->getTags(), 'title', 'id');
    parent::init();
  }

}
