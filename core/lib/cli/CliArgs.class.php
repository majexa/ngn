<?php

/**
 * Аргументы, необсходимые для запуска комманды
 */
class CliArgs {

  public $class, $method, $params;

  function __construct(CliAccessAbstract $cliHelp) {
    if ($cliHelp->oneClass) {
      $this->class = $cliHelp->oneClass;
      $this->method = $cliHelp->argv[0];
      $this->params = array_slice($cliHelp->argv, 1);
    }
    else {
      $this->class = $cliHelp->name2class($cliHelp->argv[0]);
      $methods = $cliHelp->_getVisibleMethods($this->class);
      if (count($methods) == 1) {
        $this->method = $methods[0]->name;
        $this->params = array_slice($cliHelp->argv, 1);
      } else {
        $this->method = $cliHelp->argv[1];
        $this->params = array_slice($cliHelp->argv, 2);
      }
    }
    if (!preg_match('/[a-z0-9_]/i', $this->method)) throw new Exception("Error in method arg '{$this->method}'");
    Misc::checkEmpty($this->class, '$this->class');
    Misc::checkEmpty($this->method, '$this->method');
  }

}