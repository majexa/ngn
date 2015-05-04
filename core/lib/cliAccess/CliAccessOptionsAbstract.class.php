<?php

abstract class CliAccessOptionsAbstract extends CliAccess {

  protected function renderClassOptions($class) {
    return '';
  }

  protected function renderClassRequiredOptions($class) {
    return '';
  }

  function getClasses() {
    return ClassCore::getDescendants('ArrayAccessebleOptions', ucfirst($this->prefix()));
  }

  function _getMethods($class) {
    return array_filter(parent::_getMethods($class), function (ReflectionMethod $method) {
      if (!Misc::hasPrefix('a_', $method->getName())) return false;
      return true;
    });
  }

  protected function _getParameters(ReflectionMethod $method, $class) {
    $options = $class::$requiredOptions;
    foreach ($options as &$v) $v = $this->option($method, $v);
    return array_merge($options, $this->getMethodOptionsWithMeta($method));
  }

  protected function getMethodOptionsWithMeta(ReflectionMethod $method) {
    if (!($options = ClassCore::getDocComment($method->getDocComment(), 'options'))) return [];
    $r = [];
    foreach (array_map('trim', explode(',', $options)) as $opt) {
      $r[] = $this->option($method, $opt);
    }
    return $r;
  }

  private function option(ReflectionMethod $method, $name) {
    if ($name[0] == '@') {
      $name = ltrim($name, '@');
      $helpMethod = 'helpOpt_'.$name;
      $class = $method->class;
      $variants = implode('|', $class::$helpMethod());
    } else {
      $variants = false;
    }
    return [
      'name'     => $name,
      'optional' => false,
      'variants' => $variants,
      'descr'    => false
    ];
  }

  protected function getMethod(ReflectionMethod $method) {
    return Misc::removePrefix('a_', $method->getName());
  }

  protected function _run(CliAccessArgsArgs $args) {
    $args->method = 'a_'.$args->method;
    if (is_subclass_of($args->class, 'CliAccessOptionsMultiWrapper')) {
      $this->runMultiWrapper($args);
    }
    else {
      $requiredOptions = [];
      $class = $args->class;
      foreach ($class::$requiredOptions as $i => $name) $requiredOptions[$name] = $args->params[$i];
      (new $class(array_merge( //
        $requiredOptions, //
        $this->getMethodOptionsWithParams($args, count($class::$requiredOptions)) //
      )))->{$args->method}();
    }
  }

  protected function runMultiWrapper(CliAccessArgsArgs $args) {
    $realClass = method_exists($args->class, $args->method) ? $args->class : $this->getSingleProcessorClass($args->class);
    $realArgs = clone $args;
    $realArgs->class = $realClass;
    $requiredOptions = [];
    /* @var CliAccessOptionsMultiWrapper $multiWrapper */
    $class = $args->class;
    // foreach ($realClass::$requiredOptions as $i => $name) $requiredOptions[$name] = $args->params[$i];
    $options = array_merge($requiredOptions, $this->getMethodOptionsWithParams( //
      $realArgs, //
      count($realClass::$requiredOptions) //
    ));
    $multiWrapper = (new $class($options));
    $multiWrapper->action($realArgs->method);
  }

  /**
   * Берёт класс и метод из объекта $args.
   * Получает опции метода из док-блока.
   * Формирует массив, гдк ключ - это опция, а значение - взято из объекта $args по порядку,
   * начиная с указанного отступа.
   * Возвращает этот массив.
   *
   * @param CliAccessArgsArgs $args
   * @param $offset
   * @return array
   */
  protected function getMethodOptionsWithParams(CliAccessArgsArgs $args, $offset) {
    $r = [];
    if (($options = ($this->getMethodOptions((new ReflectionMethod($args->class, $args->method)))))) {
      foreach ($options as $i => $opt) {
        $r[$opt['name']] = $args->params[$i + $offset];
        //if ($opt['name'] == 'params') $r[$opt['name']] = Cli::strParamsToArray($r[$opt['name']]);
      }
    }
    return $r;
  }

  protected function getMethodOptions(ReflectionMethod $method) {
    $optionNames = $this->getMethodOptionsWithMeta($method);
    foreach ($optionNames as &$v) $v = Misc::removePrefix('@', $v);
    return $optionNames;
  }

  protected function getSingleProcessorClass($multipleProcessorClass) {
    return rtrim($multipleProcessorClass, 's');
  }

  protected function cleanMethodName($method) {
    return Misc::removePrefix('a_', $method);
  }

}