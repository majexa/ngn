<?php

class FieldEProfileFields extends FieldESelect {

  protected function defineOptions() {
    parent::defineOptions();
    $this->options['options'] = Arr::get(O::get('DdFields', 'profile')->fields, 'title', 'name');
  }


}