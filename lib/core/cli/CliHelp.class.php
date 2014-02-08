<?php

abstract class CliHelp {

  protected $filter = [];

  protected $oneEntry = false;

  protected $argv;

  function __construct($argv) {
    if (is_string($argv)) $argv = explode(' ', $argv);
    elseif (is_array($argv)) $argv = array_slice($argv, 1, count($argv));
    else throw new Exception('Wrong type');
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
      if ($method->isConstructor()) return false;
      return $method->isPublic();
    });
  }

  protected function help() {
    print O::get('CliColors')->getColoredString('name', 'darkGray')." - optional param\n";
    print O::get('CliColors')->getColoredString('[...]', 'green')." - param options\n";
    print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
    foreach ($this->getClasses() as $v) {
      foreach ($this->getMethods($v['class']) as $vv) {
        $name = $this->oneEntry ? '' : $v['name'].' ';
        print O::get('CliColors')->getColoredString($this->runner(), 'brown')." $name{$vv['method']} ".$this->renderOptions($vv['options'])."\n";
      }
      if ($this->hasMultiWrapper($v['class'])) {
        $cmdName = lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $v['class']));
        print O::get('CliColors')->getColoredString($this->runner(), 'brown')." {$cmdName}s ".O::get('CliColors')->getColoredString("{the same options as $cmdName}", 'cyan')."\n";
      }
    }
    $this->extraHelp();
  }

  protected function hasMultiWrapper($class) {
    $class = $class.'s';
    return class_exists($class) and is_subclass_of($class, 'CliHelpMultiWrapper');
  }

  protected function run() {
    if ($this->oneEntry) {
      $class = ucfirst($this->prefix()).ucfirst($this->oneEntry);
      $method = $this->argv[0];
      $params = array_slice($this->argv, 1, count($this->argv));
    }
    else {
      $class = ucfirst($this->prefix()).ucfirst($this->argv[0]);
      $method = $this->argv[1];
      $params = array_slice($this->argv, 2, count($this->argv));
    }
    if (!$this->check($class, $method, $params)) return;
    $this->_run($class, $method, $params);
  }

  protected function check($class, $method, $params) {
    if (is_subclass_of($class, 'CliHelpMultiWrapper')) $_class = Misc::removeSuffix('s', $class);
    else $_class = $class;
    $methods = Arr::get($this->getMethods($_class), 'options', 'method');
    if (!isset($methods[$method])) {
      output("Method '$method' does not exists in class '$class'");
      return false;
    }
    if (!is_subclass_of($class, 'CliHelpMultiWrapper')) {
      foreach ($methods[$method] as $n => $param) {
        if ($param['optional']) continue;
        if (!isset($params[$n])) {
          output("Param #".($n + 1)." '{$param['name']}' is required");
          return false;
        }
      }
    }
    return true;
  }

  abstract protected function _run($class, $method, $params);

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

}