<?php

class TestSflmJs extends ProjectTestCase {

  static function setUpBeforeClass() {
    Sflm::$frontend = 'custom';
  }

  function testExistsInExistingObjects() {
    Sflm::flm('js')->addObject('Ngn.Frm');
    Sflm::reset('js');
    print "\n***".count(Sflm::flm('js')->classes->existingObjects);
    //Sflm::flm('js')->addObject('Ngn.Frm');
  }

}