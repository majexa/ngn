<?php

class TestProjectsIndexAvailable extends NgnTestCase {

  function test() {
    $curl = new Curl;
    foreach (include NGN_ENV_PATH.'/config/projects.php' as $v) {
      $this->assertTrue($curl->getObj("http://{$v['domain']}")->getAllHeaders()[0]['Code'] == 200, "{$v['domain']} available");
    }
  }

}