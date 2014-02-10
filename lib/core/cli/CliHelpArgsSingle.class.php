<?php

class CliHelpArgsSingle extends CliHelpArgs {
  use CliHelpArgsExt;

  function __construct($argv, $object) {
    $this->oneClass = get_class($object);
    parent::__construct($argv);
  }

  protected function prefix() {
    return false;
  }

  protected function runner() {
    return lcfirst($this->oneClass);
  }

}