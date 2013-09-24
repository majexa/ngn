<?php

class TestProjectsIndexAvailable extends NgnTestCase {

  function test() {
    $projects = require NGN_ENV_PATH.'/config/projects.php';
    $curl = new Curl;
    foreach ($projects as $v) $this->assertFalse((bool)strstr($curl->get("http://{$v['domain']}"), 'error:'), "{$v['domain']}: index page not available");
  }

}