<?php

abstract class CliHelp {

  function __construct($argv) {
    if (is_string($argv)) $argv = explode(' ', $argv);
    if (!isset($argv[1]) or ($class = $argv[1]) == 'help') {
      //print "{} - required param\n";
      //print "[] - param options\n";
      //print "---------\n";
      $classes = (ClassCore::getDescendants('ArrayAccessebleOptions', ucfirst($this->prefix())));
      foreach ($classes as $v) {
        $class = $v['class'];
        $methods = (new ReflectionClass($v['class']))->getMethods();
        foreach ($methods as $method) {
          if (!Misc::hasPrefix('a_', $method->getName())) continue;
          $opt = $this->getMethodOptionsWithMeta($method);
          print $this->prefix()." {$v['name']} ".Misc::removePrefix('a_', $method->getName());
          foreach ($class::$requiredOptions as $vv) print ' '.$vv;
          if (!$opt) {
            print "\n";
            continue;
          }
          foreach ($opt as &$vv) if ($vv[0] == '@') {
            $vv = Misc::removePrefix('@', $vv);
            $method = 'helpOpt_'.$vv;
            if (method_exists($class, $method)) $vv .= '['.($class::$method()).']';
          }
          print ' '.implode(' ', $opt)."\n";
        }
        $manyClass = $class.'s';
        if (class_exists($manyClass)) {
          $cmdName = lcfirst(Misc::removePrefix(ucfirst($this->prefix()), $class));
          print $this->prefix()." {$cmdName}s {the same options as $cmdName}\n";
        }
      }
      $this->extraHelp();
    } else {
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
        } else {
          $options = $this->getClassMethodOptions($argv, $this->getSingleProcessorClass($class), $method);
        }
        (new $class(array_merge($options, $options)))->action($argv[2]);
      }
      else {
        $_options = $this->getClassMethodOptions($argv, $class, $method, count($options));
        (new $class(array_merge($_options, $options)))->$method();
      }
    }
  }

  protected function extraHelp() {}

  abstract protected function prefix();

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

  protected function getMethodOptionsWithMeta(ReflectionMethod $method) {
    $options = ClassCore::getDocComment($method->getDocComment(), 'options');
    if (!$options) return [];
    return array_map('trim', explode(',', $options));
  }

  protected function getSingleProcessorClass($multipleProcessorClass) {
    return rtrim($multipleProcessorClass, 's');
  }

}