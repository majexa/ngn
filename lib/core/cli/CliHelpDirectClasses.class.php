<?php

abstract class CliHelpDirectClasses extends CliHelpArgs {

  protected function run_() {
    throw new Exception('check, run refactor');
    $class = $this->name2class($this->argv[0]);
    $method = $this->argv[1];
    $params = array_slice($this->argv, 2, count($this->argv));
    if (!$this->check($class, $method, $params)) return;
    $this->_run($class, $method, $params);
  }

}
