<?php

class CliAccessArgsSingleSub extends CliAccessArgsSingle {

  protected $_runner, $name, $cmdNameSuffix = '';

  /**
   * @param CliArgs $args
   * @param string
   * @param string
   */
  function __construct(CliArgs $args, $_runner, $name) {
    $this->_runner = $_runner;
    $this->name = $name;
    if (($constructorParams = $this->getConstructorParamsImposed($args->class, $args->params))) {
      $this->cmdNameSuffix = ' '.implode(' ', $constructorParams);
    }
    parent::__construct(array_merge([null, $args->method], $args->params), $args->class);
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

  protected function name($class) {
    return $this->name;
  }

}