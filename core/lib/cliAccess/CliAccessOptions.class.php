<?php

class CliAccessOptions extends CliAccessOptionsAbstract {

  protected $prefix;

  function __construct($argParams, $prefix, array $options = []) {
    $this->prefix = $prefix;
    parent::__construct($argParams, $options);
  }

  function prefix() {
    return $this->prefix;
  }

}