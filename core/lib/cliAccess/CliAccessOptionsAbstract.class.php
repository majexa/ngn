<?php

abstract class CliAccessOptionsAbstract extends CliAccess {

  protected function renderClassOptions($class) {
    return '';
  }

  protected function renderClassRequiredOptions($class) {
    return '';
  }

  protected function showHelpForOneClass() {
    return isset($this->argParams[0]) and !isset($this->argParams[1]) and in_array($this->argParams[0], Arr::get($this->getClasses(), 'name'));
  }

  protected function isHelp() {
    if ($this->showHelpForOneClass()) {
      return true;
    }
    return parent::isHelp();
  }

  protected function getHelpClasses() {
    if ($this->showHelpForOneClass()) {
      return array_filter($this->getClasses(), function (array $v) {
        return $v['name'] == $this->argParams[0];
      });
    }
    return parent::getHelpClasses();
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
    if ($name[0] == '{' and $name[strlen($name) - 1] == '}') {
      $name = trim($name, '{}');
      $optional = true;
    }
    else {
      $optional = false;
    }
    if ($name[0] == '@') {
      $name = ltrim($name, '@');
      $helpMethod = 'helpOpt_'.$name;
      $class = $method->class;
      $variants = implode('|', $class::$helpMethod());
    }
    else {
      $variants = false;
    }
    return [
      'name'     => $name,
      'optional' => $optional,
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
    // Получаем имя класса, у которого будет вызван экшн
    $realClass = method_exists($args->class, $args->method) ? $args->class : $this->getSingleProcessorClass($args->class);
    $realArgs = clone $args;
    $realArgs->class = $realClass;
    /* @var CliAccessOptionsMultiWrapper $multiWrapper */
    $class = $args->class;
    $options = $this->getMethodOptionsWithParams($realArgs);
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
   * @param integer $offset С какого параметра начинать брать значения
   * @return array
   */
  protected function getMethodOptionsWithParams(CliAccessArgsArgs $args, $offset = 0) {
    $r = [];
    if (($options = ($this->getMethodOptions((new ReflectionMethod($args->class, $args->method)))))) {
      foreach ($options as $i => $opt) {
        if (!isset($args->params[$i + $offset])) {
          if ($opt['optional']) continue;
          throw new Exception("Required option #".($i + 1)." '{$opt['name']}' of method {$args->class}::{$args->method} not defined");
        }
        $r[$opt['name']] = $args->params[$i + $offset];
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