<?php

class FieldERadio extends FieldECheckbox {

  public $inputType = 'radio';

  public $options = [
    'type' => 'radio'
  ];

  protected function initDefaultValue() {
    //foreach ($this->options['options'] as $k => $v) if (!strstr($k, 'disabled')) $opt[$k] = $v;
    //$this->defaultValue = (!empty($opt)) ? Arr::firstKey($opt) : Arr::firstKey($this->options['options']);
    $this->defaultValue = Arr::firstKey($this->options['options']);
  }

}