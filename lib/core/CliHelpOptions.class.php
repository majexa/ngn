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

}