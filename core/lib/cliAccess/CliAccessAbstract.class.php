<?php

abstract class CliAccessAbstract {
  use Options;

  protected $initArgv;

  function __construct($argv, array $options = []) {
    $this->setOptions($options);
    $this->initArgv = $argv;
    if (is_string($argv)) $argv = explode(' ', $argv);
    elseif (is_array($argv)) $argv = array_slice($argv, 1);
    else throw new Exception('Wrong type');
    $this->argv = $argv;
    $this->init();
    if (empty($this->options['disableRun'])) {
      if (empty($this->argv[0]) or $this->argv[0] == 'help') {
        $this->help();
      }
      else {
        $this->run();
      }
    }
  }

  protected function init() {
  }

  protected function checkConsistency() {
    foreach ($this->getClasses() as $v) {
      if (($optionalArgs = $this->getConstructorOptionalParams($v['class']))) {
        foreach ($this->getMethods($v['class']) as $m) {
          if (empty($m['options'])) continue;
          $constructorArgs = Tt()->enum(Arr::get($optionalArgs, 'name'), ', ', '`"`.$v.`"`');
          $methodArgs = Tt()->enum(Arr::get($m['options'], 'name'), ', ', '`"`.$v.`"`');
          throw new Exception(<<<TEXT
CliAccess system does not supports both usage of constructor optional arguments & method arguments.
* Remove arguments $methodArgs from "{$m['method']}" method of "{$v['class']}" class OR
* Remove optional arguments $constructorArgs from class "{$v['class']}" constructor
TEXT
          );
        }
      }
    }
  }

  abstract function prefix();

  /**
   * @return mixed [['name' => 'asd', 'class' => 'Wsd]]
   */
  abstract function getClasses();

  protected function runner($color = 'brown') {
    return O::get('CliColors')->getColoredString($this->_runner(), $color);
  }

  abstract protected function _runner();

  abstract protected function run();

  function class2name($class) {
    $r = Arr::get($this->getClasses(), 'name', 'class');
    if (!isset($r[$class])) throw new EmptyException("$r[$class]");
    return $r[$class];
  }

  function name2class($name) {
    $r = Arr::get($this->getClasses(), 'class', 'name');
    if (!isset($r[$name])) throw new EmptyException("Class by name '$name' does not exists");
    return $r[$name];
  }

  abstract protected function _getParameters(ReflectionMethod $method, $class);

  public $argv, $oneClass = false;

  function _getMethods($class) {
    return array_values(array_filter((new ReflectionClass($class))->getMethods(), function (ReflectionMethod $method) use ($class) {
      if ($method->isConstructor()) return false;
      if ($method->isStatic()) return false;
      if (Misc::hasPrefix('__', $method->name)) return false;
      return $method->isPublic();
    }));
  }

  function _getVisibleMethods($class) {
    return array_values(array_filter($this->_getMethods($class), function (ReflectionMethod $method) {
      if ($method->name == 'help') return false;
      return $method->name[0] != '_';
    }));
  }

  protected $separateParentMethods = false;

  protected function getMethodsWithoutParents($class) {
    return array_filter($this->getMethods($class), function ($v) use ($class) {
      return (new ReflectionMethod($class, $v['method']))->getDeclaringClass()->getName() == $class;
    });
  }

  protected function renderClassTitle($class) {
    if (!($title = ClassCore::title($class))) return;
    print O::get('CliColors')->getColoredString($title.':', 'green')."\n";
  }

  protected function help() {
    if (!CliAccess::$disableDescription) {
      if (!CliAccess::$proMode) print O::get('CliColors')->getColoredString('name', 'darkGray')." - optional param\n";
      if (!CliAccess::$proMode) print O::get('CliColors')->getColoredString('[...]', 'green')." - param options\n";
    }
    $classes = $this->getClasses();
    if ($classes) {
      if (!CliAccess::$proMode and !CliAccess::$disableDescription) print O::get('CliColors')->getColoredString('Supported commands:', 'yellow')."\n";
      if ($this->separateParentMethods) {
        $parentClassesOutputed = [];
        foreach ($classes as $v) {
          $this->renderClassTitle($v['class']);
          if (isset($v['title'])) print O::get('CliColors')->getColoredString($v['title'].':', 'purple')."\n";
          if (($parents = ClassCore::getParents($v['class']))) {
            $parentClass = $parents[0];
            if (!isset($parentClassesOutputed[$parentClass])) {
              print $this->_renderMethods($v['class'], $this->getMethods($parentClass), 'green');
              $parentClassesOutputed[$parentClass] = $v['class'];
            }
            else {
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
          $this->renderClassTitle($v['class']);
          if (isset($v['title'])) print O::get('CliColors')->getColoredString($v['title'].':', 'purple')."\n";
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
    return is_subclass_of($class, 'CliAccessOptionsMultiWrapper');
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

  abstract protected function renderClassOptions($class);

  abstract protected function renderClassRequiredOptions($class);

  protected function _renderMethods($class, array $methods, $runnerColor = 'brown') {
    $name = $this->cmdName($class);
    $s = '';

    foreach ($methods as $method) {
      $nameCmd = $name ? ' '.$name : '';
      $rOptions = $this->renderMethodOptions($method['options']);
      $rOptions = $rOptions ? ' '.$rOptions : '';
      if (!empty($method['title']) and getOS() == 'win') $method['title'] = Misc::transit($method['title'], false, false);
      if (CliAccess::$proMode) $help = '';
      else $help = ($method['title'] ? O::get('CliColors')->getColoredString(' -- '.$method['title'], 'cyan') : '');
      $s .= //
        $this->runner($runnerColor). // runner
        $nameCmd.(count($methods) == 1 ? '' : ' '.$method['method']). // method
        $this->renderClassRequiredOptions($class).$this->renderClassOptions($class).$rOptions. // options
        $help."\n"; //
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

  protected function getConstructorRequiredParams($class) {
    if (!($constructor = (new ReflectionClass($class))->getConstructor())) return [];
    return array_filter($constructor->getParameters(), function (ReflectionParameter $param) {
      return !$param->isOptional();
    });
  }

  protected function getConstructorOptionalParams($class) {
    if (!($constructor = (new ReflectionClass($class))->getConstructor())) return [];
    return array_filter($constructor->getParameters(), function (ReflectionParameter $param) {
      return $param->isOptional();
    });
  }

  protected function getConstructorParamsImposed($class, array $imposeParams) {
    $r = [];
    foreach ($this->getConstructorParams($class) as $n => $param) {
      $r[$n] = isset($imposeParams[$n]) ? $imposeParams[$n] : $param->getName();
    }
    return $r;
  }

  protected function classHasOptionalConstructorArgs($class) {
    if (!($constructor = (new ReflectionClass($class))->getConstructor())) return false;
    return (bool)array_filter($constructor->getParameters(), function (ReflectionParameter $param) {
      return $param->isOptional();
    });
  }

  protected function check(CliAccessArgsArgs $args) {
    if ($this->isMultiWrapper($args->class)) return true; // $_class = Misc::removeSuffix('s', $args->class);
    else $_class = $args->class;
    if (empty($args->method)) {
      output("Choose method (#3 param)");
      print $this->renderMethods($args->class);
      return false;
    }
    $methods = Arr::get($this->getMethods($_class, false), 'options', 'method');
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
    // check method required params
    foreach ($methods[$args->method] as $n => $param) {
      if ($param['optional']) continue;
      if (!isset($args->params[$n])) {
        output("Param #".($n + 1)." '{$param['name']}' is required");
        return false;
      }
    }
    return true;
  }

  abstract protected function _run(CliAccessArgsArgs $args);

  protected function renderMethodOptions($options) {
    return implode(' ', array_map(function ($v) {
      return (!empty($v['optional']) ? '{'.O::get('CliColors')->getColoredString($v['name'], 'darkGray').'}' : $v['name']). //
      ($v['variants'] ? O::get('CliColors')->getColoredString("[{$v['variants']}]", 'green') : '');
    }, $options));
  }

  protected function extraHelp() {
  }

  abstract protected function getMethod(ReflectionMethod $method);

  protected function getOptions(ReflectionMethod $method, $class) {
    return $this->_getParameters($method, $class);
  }

  /**
   * @var bool Брать методы только из текущего класса, а не из его предков
   */
  protected $filterByCurrentClass = false;

  function getMethods($class, $onlyVisible = true) {
    if ($class instanceof CliAccessMultiWrapper) {
      $class = $class::singleClass();
    }
    $methods = $onlyVisible ? $this->_getVisibleMethods($class) : $this->_getMethods($class);
    if ($this->filterByCurrentClass) {
      $methods = array_filter($methods, function (ReflectionMethod $method) use ($class) {
        return $method->class == $class;
      });
    }
    return array_map(function (ReflectionMethod $method) use ($class) {
      return [
        'options' => $this->getOptions($method, $class),
        'title'   => ClassCore::getDocComment($method->getDocComment(), 'title'),
        'method'  => $this->getMethod($method)
      ];
    }, $methods);
  }

}
