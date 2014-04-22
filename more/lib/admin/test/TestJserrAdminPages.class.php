<?php

class TestJserrAdminPages extends ProjectTestCase {

  function test() {
    $html = Ganon::strGetDom((new Curl)->get('http://'.SITE_DOMAIN.'/god?forceAuth=1'));
    foreach ($html->select('.navTop a') as $v) {
      if (!ltrim($v->href, '/') or strstr($v->href, 'logout')) {
        output2(strip_tags($v->getInnerText())." ($v->href) skipped");
        continue;
      }
      $url = SITE_DOMAIN.$v->href.'?forceAuth=1';
      output3("Processing: $url");
      if (($lines = `jserr $url`)) {
        foreach (explode("\n", $lines) as $v) {
          $v = json_decode($v);
          if (!$v) throw new Exception('!!');
          $this->assertTrue(false, $v[0].'. url: '.$url);
        }
      }
    }
  }

}