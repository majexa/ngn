<?php

/**
 * Аргументы, необсходимые для запуска комманды
 */
class CliArgs {

  public $class, $method, $params;

  function __construct(CliHelpAbstract $cliHelp) {
    if ($cliHelp->oneClass) {
      $this->class = $cliHelp->oneClass;
      $this->method = $cliHelp->argv[0];
      $this->params = array_slice($cliHelp->argv, 1, count($cliHelp->argv));
    }
    else {
      $this->class = $cliHelp->name2class($cliHelp->argv[0]);
      $this->method = $cliHelp->argv[1];
      $this->params = array_slice($cliHelp->argv, 2, count($cliHelp->argv));
    }
    Misc::checkEmpty($this->class, '$this->class');
    Misc::checkEmpty($this->method, '$this->method');
  }

}