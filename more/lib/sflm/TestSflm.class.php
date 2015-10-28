<?php

class TestSflm extends ProjectTestCase {

  function testFrontendPackagesOnDuplicates() {
    $types = [
      'css',
      'js'
    ];
    $frontends = [
      'default',
      'admin'
    ];
    foreach ($types as $type) {
      foreach ($frontends as $frontend) {
        $f = Sflm::frontend($type, $frontend);
        $f->initStore();
        foreach (array_count_values($f->parseWebCachePaths()) as $path => $count) {
          $this->assertTrue($count === 1, "Count must be 1 (type=$type, frontend=$frontend, path=$path, count=$count)");
        }
      }
    }
  }

}
