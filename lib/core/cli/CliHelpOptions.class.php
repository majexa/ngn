<?php

abstract class CliHelpOptions extends CliHelp {

  protected function getClasses() {
    return (ClassCore::getDescendants('ArrayAccessebleOptions', ucfirst($this->prefix())));
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

  protected function _run($class, $method, $params) {
    $options = [];
    $method = 'a_'.$method;
    foreach ($class::$requiredOptions as $i => $name) $options[$name] = $params[$i];
    if (is_subclass_of($class, 'CliHelpMultiWrapper')) {
      if (method_exists($class, $method)) {
        $_options = $this->getClassMethodOptions($this->argv, $class, $method);
      }
      else {
        $_options = $this->getClassMethodOptions($this->argv, $this->getSingleProcessorClass($class), $method);
      }
      /* @var CliHelpMultiWrapper $multiWrapper */
      $options = array_merge($options, $_options);
      $multiWrapper = (new $class($options));
      $multiWrapper->action($method);
    }
    else {
      $_options = $this->getClassMethodOptions($this->argv, $class, $method, count($options));
      (new $class(array_merge($options, $_options)))->$method();
    }
  }

  protected function getClassMethodOptions(array $argv, $class, $method, $argvOffset = 0) {
    $options = [];
    if (($optionNames = ($this->getMethodOptions((new ReflectionMethod($class, $method)))))) {
      $args = array_slice($argv, 3 + $argvOffset);
      foreach ($optionNames as $i => $opt) {
        if (!isset($args[$i])) throw new Exception("Option '$opt' for method '$method' not defined");
        $options[$opt] = $args[$i];
      }
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