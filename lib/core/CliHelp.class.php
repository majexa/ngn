<?php

abstract class CliHelp {

  protected $filter = [];

  protected $oneEntry = false;

  protected $argv;

  function __construct($argv) {
    if (is_string($argv)) $argv = explode(' ', $argv);
    $this->argv = $argv;
    if (empty($this->argv[0]) or $this->argv[0] == 'help') {
      $this->help();
    }
    else {
      $this->run();
    }
  }

  protected function getClasses() {
    if ($this->oneEntry) $this->filter = [$this->oneEntry];
    $classes = array_filter(array_map(function ($class) {
      return [
        'class' => $class,
        'name'  => lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $class))
      ];
    }, ClassCore::getClassesByPrefix(ucfirst($this->prefix()))));
    if (!$this->filter) return $classes;
    return array_filter($classes, function ($v) {
      return in_array($v['name'], $this->filter);
    });
  }

  protected function _getMethods($class) {
    return array_filter((new ReflectionClass($class))->getMethods(), function (ReflectionMethod $method) use ($class) {
      return $method->isPublic();
    });
  }

  protected function help() {
    print O::get('CliColors')->getColoredString('name', 'darkGray')." - optional param\n";
    print O::get('CliColors')->getColoredString('[...]', 'green')." - param options\n";
    print "---------\n";
    print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
    foreach ($this->getClasses() as $v) {
      foreach ($this->getMethods($v['class']) as $vv) {
        $name = $this->oneEntry ? '' : $v['name'].' ';
        print O::get('CliColors')->getColoredString($this->runner(), 'brown')." $name{$vv['method']} ".$this->renderOptions($vv['options'])."\n";
      }
      $manyClass = $v['class'].'s';
      if (class_exists($manyClass)) {
        $cmdName = lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $v['class']));
        print O::get('CliColors')->getColoredString($this->runner(), 'brown')." {$cmdName}s ".O::get('CliColors')->getColoredString("{the same options as $cmdName}", 'cyan')."\n";
      }
    }
    $this->extraHelp();
  }

  protected function run() {
    if ($this->oneEntry) {
      $class = ucfirst($this->prefix()).ucfirst($this->oneEntry);
      $method = $this->argv[0];
      $params = array_slice($this->argv, 1, count($this->argv));
    }
    else {
      $class = $this->argv[0];
      $method = $this->argv[1];
      $params = array_slice($this->argv, 2, count($this->argv));
    }

    $methods = Arr::get($this->getMethods($class), 'options', 'method');
    if (!isset($methods[$method])) {
      output("Method '$method' does not exists in class '$class'");
      return;
    }
    foreach ($methods[$method] as $n => $param) {
      if ($param['optional']) continue;
      if (!isset($params[$n])) {
        output("Param #".($n+1)." '{$param['name']}' is required");
        return;
      }
    }
    call_user_func_array([new $class, $method], $params);
  }


  /*
  protected function run($argv) {
    $class = ucfirst($this->prefix()).ucfirst($class);
    $opt = array_slice($argv, 3);
    $options = [];
    $method = 'a_'.$argv[2];
    foreach ($class::$requiredOptions as $i => $name) $options[$name] = $opt[$i];
    if (!empty($class::$set)) {
      // If static property $set exists, it is multiple wrapper for single processor. And we need to
      // get method options from single processor class.
      if (method_exists($class, $method)) {
        $options = $this->getClassMethodOptions($argv, $class, $method);
      }
      else {
        $options = $this->getClassMethodOptions($argv, $this->getSingleProcessorClass($class), $method);
      }
      (new $class(array_merge($options, $options)))->action($argv[2]);
    }
    else {
      $_options = $this->getClassMethodOptions($argv, $class, $method, count($options));
      (new $class(array_merge($_options, $options)))->$method();
    }
  }
  */

  protected function renderOptions($options) {
    return implode(' ', array_map(function ($v) {
      return (!empty($v['optional']) ? O::get('CliColors')->getColoredString($v['name'], 'darkGray') : $v['name']). //
      ($v['variants'] ? O::get('CliColors')->getColoredString("[{$v['variants']}]", 'green') : '');
    }, $options));
  }

  protected function extraHelp() {
  }

  protected function runner() {
    return $this->prefix();
  }

  abstract protected function prefix();

  abstract protected function _getOptions(ReflectionMethod $method, $class);

  abstract protected function getMethod(ReflectionMethod $method);

  protected function getOptions(ReflectionMethod $method, $class) {
    $options = $this->_getOptions($method, $class);
    foreach ($options as &$opt) {
      if ($opt['name'][0] != '@') continue;
      $opt['name'] = Misc::removePrefix('@', $opt['name']);
      $helpMethod = 'helpOpt_'.$opt['name'];
      if (method_exists($class, $helpMethod)) $opt['variants'] = $class::$helpMethod();
    }
    return $options;
  }

  protected function getMethods($class) {
    $methods = array_map(function (ReflectionMethod $method) use ($class) {
      return [
        'options' => $this->getOptions($method, $class),
        'method'  => $this->getMethod($method)
      ];
    }, $this->_getMethods($class));
    return $methods;
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