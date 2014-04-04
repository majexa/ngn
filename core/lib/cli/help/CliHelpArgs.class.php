<?php

abstract class CliHelpArgs extends CliHelp {

  protected function init() {
    $this->checkConsistency();
  }

  protected function renderClassOptions($class) {
    $params = $this->getConstructorParams($class);
    $options = Arr::get(array_filter($params, function(ReflectionParameter $p) {
      return $p->isOptional();
    }), 'name');


    return ($options ? ' '.O::get('CliColors')->getColoredString(implode(' ', $options), 'darkGray') : '');
  }

  protected function renderClassRequiredOptions($class) {
    $params = $this->getConstructorParams($class);
    $options = Arr::get(array_filter($params, function(ReflectionParameter $p) {
      return !$p->isOptional();
    }), 'name');
    return ($options ? ' '.implode(' ', $options) : '');
  }

  protected function _getParameters(ReflectionMethod $method, $class) {
    $params = $method->getParameters();
    $r = [];
    $meta = ClassCore::getDocComment($method, 'param');
    for ($i = 0; $i < count($params); $i++) {
      /* @var ReflectionParameter $param */
      $param = $params[$i];
      $r[] = [
        'name'     => $param->getName(),
        'optional' => $param->isOptional(),
        'variants' => (isset($meta[$i][1]) and strstr($meta[$i][1], '|')) ? $meta[$i][1] : false,
        'descr'    => (isset($meta[$i][1]) and !strstr($meta[$i][1], '|')) ? $meta[$i][1] : false,
      ];
    }
    return $r;
  }

  protected function getMethod(ReflectionMethod $method) {
    return $method->getName();
  }

  protected function _run(CliArgs $args) {
    $refl = (new ReflectionClass($args->class));
    if (($constructor = $refl->getConstructor()) and ($_constructorParams = $constructor->getParameters())) {
      $constructorParams = array_slice($args->params, 0, count($_constructorParams));
      $params = array_slice($args->params, count($_constructorParams));
      $obj = $refl->newInstanceArgs($constructorParams);
      return call_user_func_array([$obj, $args->method], $params);
    } else {
      if (!$this->check($args)) return false;
      return call_user_func_array([new $args->class, $args->method], $args->params);
    }
  }

}