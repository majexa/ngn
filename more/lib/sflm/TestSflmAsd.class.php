<?php

class TestSflmAsd extends ProjectTestCase {

  function testImplementsExtendsAddsBefore() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'default');
    Sflm::frontend('js')->addClass('Ngn.B1');
    Sflm::frontend('js')->store();
    $c = file_get_contents(Sflm::$webPath.'/js/cache/default.js');
    //die2($c);
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.C1.Ooo'), '"Extends: ..." pattern error');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.A1.Ooo'), '"Implements: ..." pattern error');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.D1'), '"Implements: ..." pattern error');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.E1'), '"e1: ..." pattern error');
  }


}