<?php

class UestPatcherLocal extends ProjectTestCase {

  static $local = true;

  function check($patcher) {
    $patcher->patch();
    foreach ($patcher->getProjectCurrentPatchIds() as $lib => $id) $this->assertTrue($patcher[$lib] == $id);
  }

  function test() {
    $this->check(new DbPatcher);
    $this->check(new FilePatcher);
  }

}