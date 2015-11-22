<?php

$_SESSION['auth'] = [
  'id'     => 1,
  'login'  => 'admin',
  'status' => 0,
  'active' => 1,
  'email'  => 'dummy@test.com',
  'phone'  => '+79202560771',
];
Sflm::$output = false;
Sflm::clearCache();

class SflmBuild {

  function testNames() {
    $r = ['index'];
    foreach (glob(PROJECT_PATH."/casper/test/*.json") as $f) {
      $r[] = str_replace('.json', '', basename($f));
    }
    return $r;
  }

  function run() {
    print `pm localProject replaceConstant nnway more BUILD_MODE true`;
    foreach ($this->testNames() as $testName) {
      $this->runTest($testName);
    }
  }

  public $effectedTests = [];

  function runTest($testName) {
    $projectName = basename(WEBROOT_PATH);
    $o = [];
    exec("cst $projectName $testName", $o, $code);
    if ($code) throw new Exception(implode("\n", $o));
    $this->effectedTests[] = str_replace(NGN_ENV_PATH.'/projects/', '', "cst: $projectName/$testName");
  }

}

(new SflmBuild)->run();

//print `pm localProject replaceConstant nnway more BUILD_MODE true`;
//travel();
//print `pm localProject replaceConstant nnway more BUILD_MODE false`;
//travel();
