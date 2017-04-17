<?php

abstract class CliAccessArgs extends CliAccess {

  protected function init() {
    $this->checkConsistency();
  }

  protected function renderClassOptions($class) {
    $params = $this->getConstructorParams($class);
    $options = Arr::get(array_filter($params, function (ReflectionParameter $p) {
      return $p->isOptional();
    }), 'name');
    return ($options ? ' '.CliColors::colored(implode(' ', $options), 'darkGray') : '');
  }

  protected function renderClassRequiredOptions($class) {
    $params = $this->getConstructorParams($class);
    $options = Arr::get(array_filter($params, function (ReflectionParameter $p) {
      return !$p->isOptional();
    }), 'name');
    return ($options ? ' '.CliColors::colored(implode(' ', $options), 'lightCyan') : '');
  }

  protected function _getParameters(ReflectionMethod $method, $class) {
    $params = $method->getParameters();
    $r = [];
    $meta = ClassCore::getDocComment($method->getDocComment(), 'param');
    for ($i = 0; $i < count($params); $i++) {
      /* @var ReflectionParameter $param */
      $param = $params[$i];
      $variants = (isset($meta[$i]['descr']) and strstr($meta[$i]['descr'], '|')) ? $meta[$i]['descr'] : false;
      $r[] = [
        'name'     => $param->getName(),
        'optional' => $param->isOptional(),
        'variants' => $variants,
        'descr'    => $meta[$i]['descr'],
      ];
    }
    return $r;
  }

  protected function getMethod(ReflectionMethod $method) {
    return $method->getName();
  }

  protected function _run(CliAccessArgsArgs $args) {
    $refl = (new ReflectionClass($args->class));
    if (($constructor = $refl->getConstructor()) and ($_constructorParams = $constructor->getParameters())) {
      // есть параметры в конструкторе
      $requiredParametersCount = $constructor->getNumberOfRequiredParameters();
      $hasOptionalParams = (bool)(count($_constructorParams) - $requiredParametersCount);
      if ($hasOptionalParams and isset($args->params[$requiredParametersCount]) and $args->params[$requiredParametersCount] == 'help') {
        // help
        $constructorParams = array_slice($args->params, 0, $requiredParametersCount);
        $obj = $refl->newInstanceArgs($constructorParams);
        foreach ($obj->help() as $cmd) {
          print $this->runner().' '.$this->class2name($args->class).' '.implode(' ', $constructorParams).' '.$cmd."\n";
        }
      } else {
        // action
        //die2($args->params);
        //die2($args);
        $constructorParams = array_slice($args->params, 0, count($_constructorParams));
        $params = array_slice($args->params, count($_constructorParams));
        $obj = $refl->newInstanceArgs($constructorParams);
        return call_user_func_array([$obj, $args->method], $params);
      }
    }
    else {
      // нет
      if (!$this->check($args)) return false;
      $r = call_user_func_array([new $args->class, $args->method], $args->params);
      if ($r instanceof CliAccessResultClass) $r->name = $args->method;
      return $r;
    }
  }

}