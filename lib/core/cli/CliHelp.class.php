<?php

// name -
abstract class CliHelp extends CliHelpAbstract {

  protected function name($class) {
    if (($prefix = $this->prefix())) {
      return lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $class));
    }
    else {
      return lcfirst($class);
    }
  }

  public function getClasses() {
    static $classes;
    if (isset($classes)) return $classes;
    if ($this->oneClass) {
      $classes = [
        [
          'class' => $this->oneClass,
          'name'  => $this->name($this->oneClass)
        ]
      ];
    }
    else {
      $classes = array_filter(array_map(function ($class) {
        return [
          'class' => $class,
          'name'  => $this->name($class)
        ];
      }, ClassCore::getClassesByPrefix(ucfirst($this->prefix()))));
    }
    return $classes;
  }

  protected function run() {
    $args = $this->getArgs();
    if (!$this->check($args)) return;
    if (($r = $this->_run($args)) and $r instanceof CliResultClass) {
      $argsSub = clone $args;
      $argsSub->class = $r->class;
      $argsSub->params = array_slice($args->params, 0, count($this->getConstructorParams($r->class)));
      $argsSub->method = $args->params[1];
      $argsSub->params = array_merge($argsSub->params, //
        array_slice($args->params, count($this->getConstructorParams($r->class)) + 1, count($args->params)));
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