<?php

abstract class CliHelpArgs extends CliHelp {

  protected function _getOptions(ReflectionMethod $method, $class) {
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
      $params = array_slice($args->params, count($_constructorParams), count($args->params));
      $obj = $refl->newInstanceArgs($constructorParams);
      return call_user_func_array([$obj, $args->method], $params);
    } else {
      return call_user_func_array([new $args->class, $args->method], $args->params);
    }
  }

}