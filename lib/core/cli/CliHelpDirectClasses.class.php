<?php

abstract class CliHelpDirectClasses extends CliHelpAbstract {

  protected function class2name($class) {
    return Arr::get($this->getClasses(), 'name', 'class')[$class];
  }

  protected function name2class($name) {
    return Arr::get($this->getClasses(), 'class', 'name')[$name];
  }

  protected function run() {
    $class = $this->name2class($this->argv[0]);
    $method = $this->argv[1];
    $params = array_slice($this->argv, 2, count($this->argv));
    if (!$this->check($class, $method, $params)) return;
    $this->_run($class, $method, $params);
  }

}
