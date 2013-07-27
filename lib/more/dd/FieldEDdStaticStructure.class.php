<?php

class FieldEDdStaticStructure extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = ['' => '— '.LANG_NOTHING_SELECTED.' —'];
    $this->options['options'] = Arr::get((new DdStructureItems)->getItems(), 'title', 'name');
  }

}