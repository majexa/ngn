<?php

class TestSflmStaticProjectDependencies extends ProjectTestCase {

  function test() {
    // get all lists: all basepaths
    for ($i = count(Ngn::$basePaths) - 1; $i >= 0; $i--) {
      foreach (glob(Ngn::$basePaths[$i].'/config/vars/sfl/js/*.php') as $file) {
        output2(basename(Ngn::$basePaths[$i]).' - '.$file);
      }
    }
  }

}