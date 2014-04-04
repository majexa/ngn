<?php

class UestProjectAdminLinks extends ProjectTestCase {

  static $local = true;

  function test() {
    $html = Ganon::fileGetDom('http://'.SITE_DOMAIN.'/admin?forceAuth=1');
    $curl = new Curl;
    foreach ($html('.navTop a') as $el) {
      if ($el->href == '/' or strstr($el->href, 'logout')) continue;
      $url = 'http://'.SITE_DOMAIN.'/'.ltrim($el->href, '/');
      $this->assertTrue($curl->check200($url), "'$url' not available");
    }
  }

}