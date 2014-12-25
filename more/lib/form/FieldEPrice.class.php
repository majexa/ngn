<?php

class FieldEPrice extends FieldEFloat {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['maxlength' => 11]);
  }

  protected function prepareValue() {
    parent::prepareValue();
    //.' <span class="gray">(руб.)</span>';
    if (!empty($this->options['value'])) $this->options['value'] = Misc::price($this->options['value']);
  }

  protected function init() {
    parent::init();
    if ($this->helpEmpty) $this->options['help'] .= 'рублей';
  }

}