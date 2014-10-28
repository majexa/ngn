<?php

class CliAccessArgsSingleSelf extends CliAccessArgsSingle {

  protected $filterByCurrentClass = true;

  function __construct($argParams) {
    parent::__construct($argParams, $this);
  }

  protected function renderClassRequiredOptions($class) {
    return '';
  }

}