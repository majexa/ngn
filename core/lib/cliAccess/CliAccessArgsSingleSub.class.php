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
    /*
    if (($constructorParams = $this->getConstructorParamsImposed($args->class, $args->params))) {
      $this->cmdNameSuffix = ' '.implode(' ', $constructorParams);
    }
    */
    //die2(;
    parent::__construct(array_merge([null], $args->params), $args->class);
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