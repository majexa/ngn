<?php

class TestProjectsIndexAvailable extends NgnTestCase {

  function test() {
    $curl = new Curl;
    foreach (include NGN_ENV_PATH.'/config/projects.php' as $v) {
      $r = $curl->get('http://'.$v['domain'], true);
      $this->assertTrue((bool)strstr($r[0], '200 OK'), "{$v['domain']} is not available");
      $this->assertFalse((bool)strstr($r[1], '<b>Warning</b>'), "{$v['domain']} has warning");
      $this->assertFalse((bool)strstr($r[1], '<b>Fatal error</b>'), "{$v['domain']} has fatal error");
    }
  }

}