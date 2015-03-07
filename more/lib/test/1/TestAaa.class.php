<?php

class TestAaa extends NgnTestCase {

  function test() {
    $a++;
    throw new Exception('!');
    print '...';
  }

}