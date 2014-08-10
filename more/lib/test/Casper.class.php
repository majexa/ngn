<?php

class Casper {

  static function run($projectName, $data) {
    $projectDir = NGN_ENV_PATH.'/projects/'.$projectName;
    $data = json_encode($data);
    if (strstr($data, "'")) throw new Exception('Data can not contains single quotes');
    $casperFolder = NGN_PATH.'/more/casper';
    $cmd = "echo '$data' | casperjs $casperFolder/test.js --projectDir=$projectDir";
    output("running $cmd");
    die2($cmd);
    $process = proc_open($cmd, [['pipe', 'r'],['pipe', 'w'],['pipe', 'w']], $pipes, realpath('./'), []);
    $buffer = '';
    if (is_resource($process)) {
      while ($s = fgets($pipes[1])) {
        print '.';
        $buffer .= $s;
        print $s;
      }
    }
  }

}