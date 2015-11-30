<?php

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
    $_SESSION['auth'] = [
      'id'     => 1,
      'login'  => 'admin',
      'active' => 1,
      'email'  => 'dummy@test.com'
    ];
    Sflm::$output = false;
    Sflm::clearCache();
    ProjectConfig::replaceConstant('more', 'BUILD_MODE', true);
    $v1 = Sflm::frontend('js', 'default')->version();
    foreach ($this->testNames() as $testName) {
      $this->runTest($testName);
    }
    print 'Origin size: '.File::format(filesize(Sflm::$webPath.'/js/origin/default.js'));
    print ', Uglified size: '.File::format(filesize(Sflm::$webPath.'/js/cache/default.js'));
    print ', Version before: '.$v1.', Version after: '.Sflm::frontend('js', 'default')->version();
    print "\n";
    // --
    ProjectConfig::replaceConstant('more', 'BUILD_MODE', false);
  }

  public $effectedTests = [];

  function runTest($testName) {
    $o = [];
    output3("cst {$this->projectName} $testName");
    exec("cst {$this->projectName} $testName", $o, $code);
    $o = implode("\n", $o);
    if ($code) throw new Exception($o);
    print $o."\n";
    $this->effectedTests[] = str_replace(NGN_ENV_PATH.'/projects/', '', "cst: {$this->projectName}/$testName");
  }

}
