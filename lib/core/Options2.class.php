<?php

class Options2 {
use Options;

  function __construct(array $options = []) {
    $this->setOptions($options);
    $this->init();
  }

  protected function init() {
  }

}
