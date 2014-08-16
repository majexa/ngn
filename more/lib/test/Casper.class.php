<?php

class Casper {

  static function run($projectName, array $steps, array $data = []) {
    $projectDir = NGN_ENV_PATH.'/projects/'.$projectName;
    $data['steps'] = $steps;
    $data = json_encode($data);
    if (strstr($data, "'")) throw new Exception('Data can not contains single quotes');
    $casperFolder = NGN_PATH.'/more/casper';
    $cmd = "echo '$data' | casperjs $casperFolder/test.js --projectDir=$projectDir";
    //output($cmd);
    $process = proc_open($cmd, [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, realpath('./'), []);
    $buffer = '';
    if (is_resource($process)) {
      while ($s = fgets($pipes[1])) {
        $buffer .= $s;
        print $s;
      }
      //$buffer = preg_replace('/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[m|K]/', '', $buffer);
      if (preg_match('/[A-Z][a-z]+Error: .*/s', $buffer, $m)) {
        throw new Exception($m[0]);
      }
    }
  }

}