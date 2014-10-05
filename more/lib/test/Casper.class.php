<?php

class Casper {

  static $shortcuts = [
    'auth' => ['thenUrl', 'admin?authLogin=admin&authPass=1234']
  ];

  static function run($projectName, array $steps, array $options = []) {
    $projectDir = NGN_ENV_PATH.'/projects/'.$projectName;
    foreach ($steps as &$step) if (is_string($step) and isset(self::$shortcuts[$step])) {
      $step = self::$shortcuts[$step];
    }
    $options['steps'] = $steps;
    $options = json_encode($options);
    if (strstr($options, "'")) throw new Exception('Data can not contains single quotes');
    $casperFolder = NGN_PATH.'/more/casper';
    $cmd = "echo '$options' | casperjs $casperFolder/test.js --projectDir=$projectDir";
    $process = proc_open($cmd, [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, realpath('./'), []);
    $buffer = '';
    if (is_resource($process)) {
      while ($s = fgets($pipes[1])) {
        $buffer .= $s;
        print $s;
      }
      if (preg_match('/[A-Z][a-z]+Error: .*/s', $buffer, $m)) {
        die2(trim($m[0]));
        throw new Exception(trim($m[0]));
      }
    }
  }

}