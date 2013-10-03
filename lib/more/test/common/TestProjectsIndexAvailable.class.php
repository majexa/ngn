<?php

class TestProjectsIndexAvailable extends NgnTestCase {

  function test() {
    $curl = new Curl;
    foreach (include NGN_ENV_PATH.'/config/projects.php' as $v) {
      $this->assertTrue($curl->check200("http://{$v['domain']}"), "{$v['domain']} not available");
    }
  }

}