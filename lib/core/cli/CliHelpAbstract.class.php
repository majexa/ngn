<?php

abstract class CliHelpAbstract {

  abstract function prefix();

  /**
   * @return mixed [['name' => 'asd', 'class' => 'Wsd]]
   */
  abstract public function getClasses();

  protected function runner($color = 'brown') {
    return O::get('CliColors')->getColoredString($this->_runner(), $color);
  }

  abstract protected function _runner();

  abstract protected function run();


  public function class2name($class) {
    $r = Arr::get($this->getClasses(), 'name', 'class');
    if (!isset($r[$class])) throw new EmptyException("$r[$class]");
    return $r[$class];
  }

  public function name2class($name) {
    $r = Arr::get($this->getClasses(), 'class', 'name');
    if (!isset($r[$name])) throw new EmptyException("$r[$name]");
    return $r[$name];
  }

  abstract protected function _getOptions(ReflectionMethod $method, $class);

  public $argv, $oneClass = false;

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
      if ($method->isStatic()) return false;
      return $method->isPublic();
    });
  }

  protected $separateParentMethods = false;

  protected function getMethodsWithoutParents($class) {
    return array_filter($this->getMethods($class), function($v) use ($class) {
      return (new ReflectionMethod($class, $v['method']))->getDeclaringClass()->getName() == $class;
    });
  }

  protected function help() {
    print O::get('CliColors')->getColoredString('name', 'darkGray')." - optional param\n";
    print O::get('CliColors')->getColoredString('[...]', 'green')." - param options\n";
    $classes = $this->getClasses();
    if ($classes) {
      print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
      if ($this->separateParentMethods) {
        $parentClassesOutputed = [];
        foreach ($classes as $v) {
          if (($parents = ClassCore::getParents($v['class']))) {
            $parentClass = $parents[0];
            if (!isset($parentClassesOutputed[$parentClass])) {
              print $this->_renderMethods($v['class'], $this->getMethods($parentClass), 'green');
              $parentClassesOutputed[$parentClass] = $v['class'];
            } else {
              print "the same options as: ".$this->runner('green').' '.$this->class2name($parentClassesOutputed[$parentClass])."\n";
            }
            print $this->_renderMethods($v['class'], $this->getMethodsWithoutParents($v['class']));
          }
          else {
            print $this->renderMethods($v['class']);
          }
        }
      }
      else {
        foreach ($classes as $v) {
          print $this->renderMethods($v['class']);
        }
      }
    }
    else {
      print O::get('CliColors')->getColoredString('No supported commands', 'red')."\n";
    }
    $this->extraHelp();
  }

  protected function hasMultiWrapper($class) {
    $class = $class.'s';
    return class_exists($class) and $this->isMultiWrapper($class);
  }

  protected function isMultiWrapper($class) {
    return is_subclass_of($class, 'CliHelpMultiWrapper');
  }

  protected function renderMethods($class) {
    if (!($methods = $this->getMethods($class))) {
      if ($this->isMultiWrapper($class)) return $this->_renderMethods($class, []);
      return O::get('CliColors')->getColoredString("No supported methods in class '$class'", 'red')."\n";
    }
    return $this->_renderMethods($class, $methods);
  }

  protected function cmdName($class) {
    return $this->oneClass ? false : $this->class2name($class);
  }

  protected function _renderMethods($class, array $methods, $runnerColor = 'brown') {
    $name = $this->cmdName($class);
    $s = '';
    // program class
    foreach ($methods as $method) {
      $nameCmd = $name ? $name.' ' : '';
      $s .= //
        $this->runner($runnerColor). //
        " $nameCmd{$method['method']} ".$this->renderOptions($method['options']). //
        ($method['title'] ? O::get('CliColors')->getColoredString(' -- '.$method['title'], 'cyan') : '').
        "\n"; //
    }
    if ($name and $this->isMultiWrapper($class)) {
      $s .= //
        $this->runner()." $name ". //
        O::get('CliColors')->getColoredString("{the same options as $name}", 'cyan')."\n";
    }
    return $s;
  }

  protected function getConstructorParams($class) {
    if (!($constructor = (new ReflectionClass($class))->getConstructor())) return [];
    return $constructor->getParameters();
  }

  protected function getConstructorParamsImposed($class, array $imposeParams) {
    $r = [];
    foreach (array_keys($this->getConstructorParams($class)) as $n) {
      //if (!)) throw new EmptyException("\$imposeParams[$n]");
      $r[$n] = isset($imposeParams[$n]) ? $imposeParams[$n] : "DUMMY-$n";
    }
    return $r;
  }

  protected function check(CliArgs $args) {
    if ($this->isMultiWrapper($args->class)) $_class = Misc::removeSuffix('s', $args->class);
    else $_class = $args->class;
    if (empty($args->method)) {
      output("Choose method (#3 param)");
      print $this->renderMethods($args->class);
      return false;
    }
    $methods = Arr::get($this->getMethods($_class), 'options', 'method');
    if (!isset($methods[$args->method])) {
      throw new Exception("Method '{$args->method}' does not exists in class '{$args->class}'");
    }
    foreach ($this->getConstructorParams($args->class) as $n => $param) {
      if ($param->isOptional()) continue;
      if (!isset($args->params[$n])) {
        output("Param #".($n + 1)." '".$param->getName()."' is required");
        return false;
      }
    }
    foreach ($methods[$args->method] as $n => $param) {
      if ($param['optional']) continue;
      if (!isset($args->params[$n])) {
        output("Param #".($n + 1)." '{$param['name']}' is required");
        return false;
      }
    }
    return true;
  }

  abstract protected function _run(CliArgs $args);

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
      ClassCore::getDocComment($method->getDocComment(), 'title');
      return [
        'options' => $this->getOptions($method, $class),
        'title' => ClassCore::getDocComment($method->getDocComment(), 'title'),
        'method'  => $this->getMethod($method)
      ];
    }, $this->_getMethods($class));
    return $methods;
  }


}