<?php

class CliHelpArgsSingleSelf extends CliHelpArgsSingle {

  protected $filterByCurrentClass = true;

  function __construct($argv) {
    parent::__construct($argv, $this);
  }

  protected function renderClassRequiredOptions($class) {
    return '';
  }

}