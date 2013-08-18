<?php

class FieldEDdMultiFields extends FieldEMultiselect {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'options' => Arr::get(O::get('DdFields', $this->form->strName)->getFields(), 'title', 'name')
    ]);
  }

}