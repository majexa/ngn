<?php

abstract class CliHelpDirectClasses extends CliHelpAbstract {

  protected function class2name($class) {
    return Arr::get($this->getClasses(), $class, 'class')['name'];
  }

  protected function name2class($name) {
    return Arr::get($this->getClasses(), $name, 'name')['class'];
  }

  protected function run() {
    $class = $this->name2class($this->argv[0]);
    $method = $this->argv[1];
    $params = array_slice($this->argv, 2, count($this->argv));
    if (!$this->check($class, $method, $params)) return;
    $this->_run($class, $method, $params);
  }

}
