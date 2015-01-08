<?php

class Casper {

  static $shortcuts = [
    'auth' => ['thenUrl', 'admin?authLogin=admin&authPass=1234']
  ];

  static function run($projectName, array $steps) {
    $projectDir = NGN_ENV_PATH.'/projects/'.$projectName;
    foreach ($steps as &$step) if (is_string($step) and isset(self::$shortcuts[$step])) {
      $step = self::$shortcuts[$step];
    }
    $steps = json_encode($steps);
    if (strstr($steps, "'")) throw new Exception('Data can not contains single quotes');
    $casperFolder = NGN_PATH.'/more/casper';
    $cmd = "echo '$steps' | casperjs $casperFolder/test.js --stepsFile=$projectDir --disableCapture=1";
    //$cmd = "echo '$steps' | casperjs $casperFolder/test.js --projectDir=$projectDir --disableCapture=1";
    print $cmd."\n\n";
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

  static function runFile($test, $projectName = null) {
    if (!$projectName) $projectName = PROJECT_KEY;
    $projectDir = NGN_ENV_PATH.'/projects/'.$projectName;
    $casperFolder = NGN_PATH.'/more/casper';
    $cmd = "casperjs $casperFolder/test.js --projectDir=$projectDir --stepsFile=$casperFolder/test/$test.js";
    //$cmd = "casperjs $casperFolder/test.js --projectDir=$projectDir --stepsFile=$casperFolder/test/$test.js --disableCapture=1";
    print `$cmd`;
  }

}