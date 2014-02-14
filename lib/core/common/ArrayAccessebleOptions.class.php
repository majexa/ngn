<?php

abstract class ArrayAccessebleOptions extends ArrayAccesseble {
use Options;

  function __construct(array $options = []) {
    $this->setOptions($options);
    $this->init();
  }

  protected function &getArrayRef() {
    return $this->options;
  }

  function init() {}

}