<?php

/**
 * Аргументы, необсходимые для запуска комманды
 */
class CliAccessArgsArgs {

  public $class, $method, $params;

  function __construct(CliAccessAbstract $cliHelp) {
    if (get_class($cliHelp) == 'CliAccessArgsSingleSub') die2('!');
    if ($cliHelp->oneClass) {
      $this->class = $cliHelp->oneClass;
      $methods = (new ReflectionClass($this->class))->getMethods();
      if (count($methods) == 1) {
        $this->method = $methods[0]->name;
        $this->params = $cliHelp->argParams;
      } else {
        $this->method = $cliHelp->argParams[0];
        if (strstr(get_class($cliHelp), 'Sub')) die2($cliHelp->argParams);

        $this->params = array_slice($cliHelp->argParams, 1);
      }
    }
    else {
      $this->class = $cliHelp->name2class($cliHelp->argParams[0]);
      $methods = $cliHelp->_getVisibleMethods($this->class);
      if (count($methods) == 1) {
        $this->method = $methods[0]->name;
        $this->params = array_slice($cliHelp->argParams, 1);
      } else {
        $this->method = $cliHelp->argParams[1];
        $this->params = array_slice($cliHelp->argParams, 2);
      }
    }
    if (!preg_match('/[a-z0-9_]/i', $this->method)) throw new Exception("Error in method arg '{$this->method}'");
    Misc::checkEmpty($this->class, '$this->class');
    Misc::checkEmpty($this->method, '$this->method');
  }

}