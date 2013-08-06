<?php

class FieldEDdMultiFields extends FieldEMultiselect {

  protected function defineOptions() {
    parent::defineOptions();
    $this->options['options'] = Arr::get(O::get('DdFields', $this->form->strName)->
      getFields(), 'title', 'name');
  }

}