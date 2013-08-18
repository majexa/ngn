<?php

class FieldEButton extends FieldEAbstract {

  public $inputType = 'button';

  protected function defineOptions() {
    return ['noValue' => true];
  }

  function _html() {
    return '<a href="#" class="btn"><span>'.$this->options['value'].'</span></a>';
  }

}