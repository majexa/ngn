<?php

<<<<<<< HEAD
=======
$_SESSION['auth'] = [
  'id'     => 1,
  'login'  => 'admin',
  'active' => 1,
  'email'  => 'dummy@test.com'
];
Sflm::$output = false;
Sflm::clearCache();

class SflmBuild {

  public $projectName;

  function __construct() {
    $this->projectName = basename(WEBROOT_PATH);
  }

  function testNames() {
    $r = ['index'];
    foreach (glob(PROJECT_PATH."/casper/test/*.json") as $f) {
      $r[] = str_replace('.json', '', basename($f));
    }
    return $r;
  }

  function run() {
    print `pm localProject replaceConstant {$this->projectName} more BUILD_MODE true`;
    foreach ($this->testNames() as $testName) {
      $this->runTest($testName);
    }
    print `pm localProject replaceConstant {$this->projectName} more BUILD_MODE false`;
  }

  public $effectedTests = [];

  function runTest($testName) {
    $o = [];
    exec("cst {$this->projectName} $testName", $o, $code);
    if ($code) throw new Exception(implode("\n", $o));
    $this->effectedTests[] = str_replace(NGN_ENV_PATH.'/projects/', '', "cst: {$this->projectName}/$testName");
  }

}

>>>>>>> eae4803... Auto-commit on 23.11.2015 17:03:29
(new SflmBuild)->run();
