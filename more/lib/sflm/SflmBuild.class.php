<?php

/**
 * Делает SFLM-билд с помощью набора Client-Side тестов
 */
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

  function run($test = null) {
    if (!empty(Sflm::$debugPaths['js']) or !empty(Sflm::$debugPaths['css'])) {
      throw new Exception('Clean Sflm::$debugPaths before running build');
    }
    if (!file_exists(PROJECT_PATH.'/config/constants/more.php')) {
      print "Creating SFLM-build skipped on '{$this->projectName}' project. 'more.php' does not exists\n";
      return;
    }
    print "Creating SFLM-build on '{$this->projectName}' project\n";
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
      if (!$test) {
        $this->runTest($testName);
        continue;
      }
      if ($test == $testName) $this->runTest($testName);
    }
    $s = "SFLM-build created";
    $s .= "\nOrigin / Uglified sizes: ". //
      (file_exists(Sflm::$webPath.'/js/origin/default.js') ? File::format(filesize(Sflm::$webPath.'/js/origin/default.js')) : 'none').//
      ' / '. //
      (file_exists(Sflm::$webPath.'/js/cache/default.js') ? File::format(filesize(Sflm::$webPath.'/js/cache/default.js')) : 'none');
    $s .= "\nVersion before / after: ".$v1.' / '.Sflm::frontend('js', 'default')->version();
    print $s."\n-------\n";
    ProjectConfig::replaceConstant('more', 'BUILD_MODE', false);
  }

  public $effectedTests = [];

  function runTest($testName) {
    $o = [];
    output3("cst {$this->projectName} $testName");
    exec("cst {$this->projectName} $testName", $o, $code);
    $o = implode("\n", $o);
    //if ($code) throw new Exception('code:'.$code."\n".$o);
    print $o."\n";
    $this->effectedTests[] = str_replace(NGN_ENV_PATH.'/projects/', '', "cst: {$this->projectName}/$testName");
  }

}
