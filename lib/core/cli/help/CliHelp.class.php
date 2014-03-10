<?php

// name -
abstract class CliHelp extends CliHelpAbstract {

  /**
   * Используется для формирования имени класса при генерации списка классов
   *
   * @param $class
   * @return string
   */
  protected function name($class) {
    if (($prefix = $this->prefix())) {
      return lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $class));
    }
    else {
      return lcfirst($class);
    }
  }

  protected $classes;

  function getClasses() {
    if (isset($this->classes)) return $this->classes;
    if ($this->oneClass) {
      $this->classes = [
        [
          'class' => $this->oneClass,
          'name'  => $this->name($this->oneClass)
        ]
      ];
    }
    else {
      $this->classes = array_filter(array_map(function ($class) {
        return [
          'class' => $class,
          'name'  => $this->name($class)
        ];
      }, ClassCore::getClassesByPrefix(ucfirst($this->prefix()))));
    }
    return $this->classes;
  }

  protected function run() {
    $args = $this->getArgs();
    if (($r = $this->_run($args)) and $r instanceof CliHelpResultClass) {
      if ($this->classHasOptionalConstructorArgs($r->class)) throw new Exception('Sub-action class can not has optional constructor arguments');
      $argsSub = clone $args;
      if ((new ReflectionClass($r->class))->isAbstract()) throw new Exception('Can not be abstract');
      $argsSub->class = $r->class;
      $argsSub->params = array_slice($args->params, 0, count($this->getConstructorParams($r->class)));
      $argsSub->method = $args->params[1];
      $argsSub->params = array_merge($argsSub->params, //
        array_slice($args->params, count($this->getConstructorParams($r->class)) + 1));
      new CliHelpArgsSingleSub($argsSub, $this->_runner(), $r->name);
    }
  }

  /**
   * @return CliArgs
   */
  protected function getArgs() {
    return new CliArgs($this);
  }

  protected function _runner() {
    return $this->prefix();
  }

}