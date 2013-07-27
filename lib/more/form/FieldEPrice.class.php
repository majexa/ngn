<?php

class FieldEPrice extends FieldEFloat {

  protected function prepareValue() {
    parent::prepareValue();
    $this->options['title'] = $this->options['title'];
      //.' <span class="gray">(руб.)</span>';
    if (!empty($this->options['value'])) $this->options['value'] = Misc::price($this->options['value']);
  }

  protected function init() {
    parent::init();
    if ($this->helpEmpty) $this->options['help'] .= 'рублей';
  }
  
}