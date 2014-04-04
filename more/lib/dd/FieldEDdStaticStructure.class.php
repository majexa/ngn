<?php

class FieldEDdStaticStructure extends FieldESelect {

  protected function defineOptions() {
    return [
      'options' => array_merge(['' => '— '.LANG_NOTHING_SELECTED.' —'], Arr::get((new DdStructureItems)->getItems(), 'title', 'name'))
    ];
  }

}