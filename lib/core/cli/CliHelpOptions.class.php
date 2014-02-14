<?php

abstract class CliHelpOptions extends CliHelp {

  public function getClasses() {
    return ClassCore::getDescendants('ArrayAccessebleOptions', ucfirst($this->prefix()));
  }

  protected function _getMethods($class) {
    return array_filter(parent::_getMethods($class), function (ReflectionMethod $method) {
      if (!Misc::hasPrefix('a_', $method->getName())) return false;
      return true;
    });
  }

  protected function _getOptions(ReflectionMethod $method, $class) {
    $options = [];
    $options = array_merge($options, $class::$requiredOptions);
    foreach ($options as &$v) $v = $this->option($v);
    return array_merge($options, $this->getMethodOptionsWithMeta($method));
  }

  protected function getMethodOptionsWithMeta(ReflectionMethod $method) {
    if (!($options = ClassCore::getDocComment($method->getDocComment(), 'options'))) return [];
    $r = [];
    foreach (array_map('trim', explode(',', $options)) as $name) $r[] = $this->option($name);
    return $r;
  }

  private function option($name) {
    return [
      'name' => $name,
      'optional' => false,
      'variants' => false,
      'descr' => false
    ];
  }

  protected function getMethod(ReflectionMethod $method) {
    return Misc::removePrefix('a_', $method->getName());
  }

  protected function _run(CliArgs $args) {
    $method = 'a_'.$args->method;
    if (is_subclass_of($args->class, 'CliHelpMultiWrapper')) {
      $this->runMultiWrapper($args->class, $method, $args->params);
    }
    else {
      $requiredOptions = [];
      $class = $args->class;
      foreach ($class::$requiredOptions as $i => $name) $requiredOptions[$name] = $args->params[$i];
      (new $class(array_merge($requiredOptions, $this->getMethodOptionsWithParams($args))))->$method();
    }
  }

  protected function runMultiWrapper(CliArgs $args) {
    $realClass = method_exists($args->class, $args->method) ? $args->class : $this->getSingleProcessorClass($args->class);
    $requiredOptions = [];
    foreach ($realClass::$requiredOptions as $i => $name) $requiredOptions[$name] = $args->params[$i];
    $realArgs = clone $args;
    $realArgs->class = $realClass;
    $options = array_merge($requiredOptions, $this->getMethodOptionsWithParams($realArgs));
    /* @var CliHelpMultiWrapper $multiWrapper */
    $class = $args->class;
    $multiWrapper = (new $class($options));
    $multiWrapper->action($args->method);
  }

  protected function getMethodOptionsWithParams(CliArgs $args) {
    if (($options = ($this->getMethodOptions((new ReflectionMethod($args->class, $args->method)))))) {
      foreach ($options as $i => $opt) $options[$opt['name']] = $args->params[$i];
    }
    return $options;
  }

  protected function getMethodOptions(ReflectionMethod $method) {
    $optionNames = $this->getMethodOptionsWithMeta($method);
    foreach ($optionNames as &$v) $v = Misc::removePrefix('@', $v);
    return $optionNames;
  }

  protected function getSingleProcessorClass($multipleProcessorClass) {
    return rtrim($multipleProcessorClass, 's');
  }


}