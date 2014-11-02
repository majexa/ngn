<?php

class CliAccessArgsSingleSub extends CliAccessArgsSingle {

  protected $_runner, $cmdPrefix, $cmdNameSuffix = '';

  /**
   * @param CliAccessArgsArgs $args
   * @param object|string $_runner Название программы
   * @param string $cmdPrefix Имя класса
   */
  function __construct(CliAccessArgsArgs $args, $_runner, $cmdPrefix) {
    $this->_runner = $_runner;
    $this->cmdPrefix = $cmdPrefix;
    parent::__construct(array_merge([null], $args->params), $args->class);
  }

  protected function isHelp() {
    $methods = $this->getPublicMethods();
    if (count($methods) == 1) {
      $requiredParams = array_filter($methods[0]->getParameters(), function (ReflectionParameter $param) {
        return !$param->isOptional();
      });
      if (count($requiredParams) > 0 and count($this->argParams) <= 1) {
        return true;
      }
    }
    return (count($this->argParams) <= count($this->getConstructorParams($this->oneClass)));
  }

  protected function getArgsOneClass() {
    $methods = (new ReflectionClass($this->oneClass))->getMethods();
    $constructorParams = $this->getConstructorParams($this->oneClass);
    if (count($methods) == 1) {
      $method = $methods[0]->name;
      $params = array_slice($this->argParams, 1, count($this->argParams));
    }
    else {
      $method = $this->argParams[1];
      $params = array_merge( //
        array_slice($this->argParams, 0, count($constructorParams)), //
        array_slice($this->argParams, 2, count($this->argParams)) //
      );
    }
    return new CliAccessArgsArgs($this->oneClass, $method, $params);
  }

  protected function renderClassRequiredOptions($class) {
    return '';
  }

  protected function cmdName($class) {
    return $this->class2name($class).$this->cmdNameSuffix;
  }

  protected function _runner() {
    return $this->_runner;
  }

  protected function className($class) {
    return $this->cmdPrefix;
  }

}