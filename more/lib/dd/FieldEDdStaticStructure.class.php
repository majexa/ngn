<?php

class FieldEDdStaticStructure extends FieldESelect {

  protected function defineOptions() {
    return [
      'options' => array_merge(['' => '— '.Locale::get('nothingSelected').' —'], Arr::get((new DdStructureItems)->getItems(), 'title', 'name'))
    ];
  }

}