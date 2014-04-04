<?php

class FieldEStaticText extends FieldEAbstract {

  public $options = [
    'noValue'   => true
  ];

  function html() {
    return $this->options['text'];
  }

}
