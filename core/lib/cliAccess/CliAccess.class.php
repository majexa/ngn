<?php

// name -
abstract class CliAccess extends CliAccessAbstract {

  /**
   * Используется для формирования имени класса при генерации списка классов
   *
   * @param $class
   * @return string
   */
  protected function className($class) {
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
          'name'  => $this->className($this->oneClass)
        ]
      ];
    }
    else {
      $this->classes = array_filter(array_map(function ($class) {
        return [
          'class' => $class,
          'name'  => $this->className($class)
        ];
      }, ClassCore::getClassesByPrefix(ucfirst($this->prefix()))));
    }
    return $this->classes;
  }

  protected function run() {
    $args = $this->getArgs();
    if (($r = $this->_run($args)) and $r instanceof CliAccessResultClass) {
      $this->runSub($args, $r);
    }
  }

  protected function runSub(CliAccessArgsArgs $args, CliAccessResultClass $r) {
    if ($this->classHasOptionalConstructorArgs($r->class)) throw new Exception('Sub-action class ('.$r->class.') can not has optional constructor arguments');
    $argsSub = clone $args;
    if ((new ReflectionClass($r->class))->isAbstract()) throw new Exception('Can not be abstract');
    $argsSub->class = $r->class;
    //die2($args);
    new CliAccessArgsSingleSub($argsSub, $this->_runner(), $r->name.' '.$args->params[0]);
  }

  /**
   * @return ReflectionMethod[]
   */
  protected function getPublicMethods() {
    return (new ReflectionClass($this->oneClass))->getMethods();
  }

  protected function getArgsOneClass() {
    $methods = $this->getPublicMethods();
    if (count($methods) == 1) {
      $method = $methods[0]->name;
      $params = $this->argParams;
    } else {
      $method = $this->argParams[0];
      $params = array_slice($this->argParams, 1);
    }
    return new CliAccessArgsArgs($this->oneClass, $method, $params);
  }

  protected function getArgs() {
    if ($this->oneClass) {
      return $this->getArgsOneClass();
    }
    else {
      $class = $this->name2class($this->argParams[0]);
      $methods = $this->_getVisibleMethods($class);
      if (count($methods) == 1) {
        $method = $this->cleanMethodName($methods[0]->name);
        $params = array_slice($this->argParams, 1);
      } else {
        $method = $this->argParams[1];
        $params = array_slice($this->argParams, 2);
      }
      return new CliAccessArgsArgs($class, $method, $params);
    }
  }

  /**
   * Импользуется для очищения реального имени метода от системных префиксов
   * @see CliAccessOptionsAbstract
   *
   * @param $method
   * @return mixed
   */
  protected function cleanMethodName($method) {
    return $method;
  }

  protected function _runner() {
    return $this->prefix();
  }

  static $proMode = false, $disableDescription = false;

}

if (getenv('HELP_DISABLE_DESCRIPTION')) CliAccess::$disableDescription = true;