<?php

class FieldEBorderSize extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = array_merge(
      ['' => 'по умолчанию'],
      Arr::toOptions([
        '0px',
        '1px',
        '2px',
        '3px',
        '4px',
        '5px',
    ]));
  }

}