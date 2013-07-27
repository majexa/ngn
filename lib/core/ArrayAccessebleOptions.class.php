<?php

class ArrayAccessebleOptions extends ArrayAccesseble {
use Options;

  function __construct(array $options = []) {
    $this->setOptions($options);
    $this->init();
  }

  function init() {}

}