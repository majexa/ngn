<?php

class TestCliAccess extends NgnTestCase {

  function test() {
    new PmManager([
      'pm.php',
      'localProject',
      'cmd',
      'update'
    ]);
  }

}