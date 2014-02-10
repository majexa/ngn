<?php

abstract class CliHelp extends CliHelpAbstract {

  abstract protected function prefix();

  protected function getClasses() {
    if ($this->oneClass) {
      return [
        [
          'class' => $this->oneClass,
          'name'  => lcfirst($this->oneClass)
        ]
      ];
    }
    return array_filter(array_map(function ($class) {
      if ($prefix = $this->prefix()) {
        $name = lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $class));
      }
      else {
        $name = lcfirst($class);
      }
      return [
        'class' => $class,
        'name'  => $name
      ];
    }, ClassCore::getClassesByPrefix(ucfirst($this->prefix()))));
    /*
    if (!$this->filter) return $classes;
    return array_filter($classes, function ($v) {
      return in_array($v['name'], $this->filter);
    });
    */
  }

  protected function run() {
    if ($this->oneClass) {
      $class = $this->oneClass;
      $method = $this->argv[0];
      $params = array_slice($this->argv, 1, count($this->argv));
    }
    else {
      $class = ucfirst($this->prefix()).ucfirst($this->argv[0]);
      $method = $this->argv[1];
      $params = array_slice($this->argv, 2, count($this->argv));
    }
    if (!$this->check($class, $method, $params)) return;
    $this->_run($class, $method, $params);
  }

  protected function class2name($class) {
    return ClassCore::classToName($this->prefix(), $class);
  }

  protected function runner() {
    return $this->prefix();
  }

}