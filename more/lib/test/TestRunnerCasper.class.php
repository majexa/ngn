<?php

class TestRunnerCasper {

  protected function files() {
    $r = [];
    foreach (Ngn::$basePaths as $path) {
      foreach (Dir::getFilesR($path, '*.test.json') as $file) {
        $r[] = $file;
      }
    }
    return $r;
  }

  protected function name($file) {
    return str_replace('.test.json', '', basename($file));
  }

  protected $projectName, $filterNames;

  function __construct($projectName, $filterNames = null) {
    $this->projectName = $projectName;
    if (is_string($filterNames)) $filterNames = Misc::quoted2arr($filterNames);
    $this->filterNames = $filterNames;
  }

  function run() {
    if ($this->filterNames) {
      $files = [];
      foreach ($this->files() as $file) {
        if (in_array($this->name($file), $this->filterNames)) $files[] = $file;
      }
    } else {
      $files = $this->files();
    }
    $projectDir = NGN_ENV_PATH.'/projects/'.$this->projectName;
    foreach ($files as $file) {
      output2($file);
      print `casperjs /home/user/ngn-env/ngn/more/casper/test.js --projectDir=$projectDir --test=$file`;
    }
  }

  static function runTest($projectName, $data) {
    $projectDir = NGN_ENV_PATH.'/projects/'.$projectName;
    $data = json_encode($data);
    if (strstr($data, "'")) throw new Exception('Data can not contains single quotes');
    $cmd = "echo '$data' | casperjs /home/user/ngn-env/ngn/more/casper/test.js --projectDir=$projectDir";
    print `$cmd`;
  }

}