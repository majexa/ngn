<?php

abstract class CliHelpAbstract {

  /**
   * @return mixed [['name' => 'asd', 'class' => 'Wsd]]
   */
  abstract protected function getClasses();

  abstract protected function runner();

  abstract protected function run();

  abstract protected function class2name($class);

  abstract protected function _getOptions(ReflectionMethod $method, $class);

  protected $filter = [];

  protected $oneClass = false;

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

  protected function _getMethods($class) {
    return array_filter((new ReflectionClass($class))->getMethods(), function (ReflectionMethod $method) use ($class) {
      if ($method->isConstructor()) return false;
      return $method->isPublic();
    });
  }

  protected function help() {
    print O::get('CliColors')->getColoredString('name', 'darkGray')." - optional param\n";
    print O::get('CliColors')->getColoredString('[...]', 'green')." - param options\n";
    $classes = $this->getClasses();
    if ($classes) {
      print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
      foreach ($classes as $v) {
        print $this->renderMethods($v['class']);
      }
    } else {
      print O::get('CliColors')->getColoredString('No supported commands', 'red')."\n";
    }
    $this->extraHelp();
  }

  protected function hasMultiWrapper($class) {
    $class = $class.'s';
    return class_exists($class) and is_subclass_of($class, 'CliHelpMultiWrapper');
  }

  protected function getMethodDescr($method) {
  }

  protected function renderMethods($class) {
    $name = $this->oneClass ? false : $this->class2name($class);
    $s = '';
    foreach ($this->getMethods($class) as $method) {
      $nameCmd = $name ? $name.' ' : '';
      $s .= O::get('CliColors')->getColoredString($this->runner(), 'brown')." $nameCmd{$method['method']} ".$this->renderOptions($method['options'])."\n";
    }
    if ($name and $this->isMultiWrapper($class)) {
      $s .= //
        O::get('CliColors')->getColoredString($this->runner(), 'brown')." {$name}s ". //
        O::get('CliColors')->getColoredString("{the same options as $name}", 'cyan')."\n";
    }
    return $s;
  }

  protected function isMultiWrapper($class) {
    return is_subclass_of($class, 'CliHelpMultiWrapper');
  }

  protected function check($class, $method, $params) {
    if ($this->isMultiWrapper($class)) $_class = Misc::removeSuffix('s', $class);
    else $_class = $class;

    if (empty($method)) {
      output("Choose method (#3 param)");
      print $this->renderMethods($class);
      return false;
    }
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