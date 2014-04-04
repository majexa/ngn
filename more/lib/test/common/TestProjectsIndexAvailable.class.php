<?php

class TestProjectsIndexAvailable extends NgnTestCase {

  function test() {
    $curl = new Curl;
    foreach (include NGN_ENV_PATH.'/config/projects.php' as $v) {
      try {
        $r = $curl->check200("http://{$v['domain']}");
      } catch (Exception $e) {
        $this->assertTrue(false, "{$v['domain']} exception: ".$e->getMessage());
        return;
      }
      $this->assertTrue($r, "{$v['domain']} not available");
    }
  }

}