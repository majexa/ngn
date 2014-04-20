<?php

class TestAaa extends ProjectTestCase {


  function test() {
    Sflm::setFrontendName('default');
    Sflm::frontend('js')->addObject('Ngn.Form.El.DdTags', "ddTags field init");
  }

}