<?php

trait CliHelpArgsExt {

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
        'descr' => (isset($meta[$i][1]) and !strstr($meta[$i][1], '|')) ? $meta[$i][1] : false,
      ];
    }
    return $r;
  }

  protected function getMethod(ReflectionMethod $method) {
    return $method->getName();
  }

  protected function _run($class, $method, $params) {
    call_user_func_array([new $class, $method], $params);
  }

}