<?php

class TestJserrAdminPages extends ProjectTestCase {

  function test() {
    $this->checkErrors(SITE_DOMAIN.'/god');
    $this->checkErrors(SITE_DOMAIN.'/god?forceAuth=1');
    $html = Ganon::strGetDom((new TestCurl)->get('http://'.SITE_DOMAIN.'/god?forceAuth=1'));
    foreach ($html->select('.navTop a') as $v) {
      if (!ltrim($v->href, '/') or strstr($v->href, 'logout')) {
        //output2(strip_tags($v->getInnerText())." ($v->href) skipped");
        continue;
      }
      $this->checkErrors(SITE_DOMAIN.$v->href.'?forceAuth=1');
    }
  }

  protected function checkErrors($url) {
    if (($lines = `jserr $url`)) {
      foreach (explode("\n", $lines) as $v) {
        $v = json_decode($v);
        if (!$v) throw new Exception('!!');
        $this->assertTrue(false, $v[0].'. url: '.$url);
      }
    }
  }

}