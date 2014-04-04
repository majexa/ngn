<?php

class FieldEProfileFields extends FieldESelect {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'options' => Arr::get(O::get('DdFields', 'profile')->fields, 'title', 'name')
    ]);
  }

}