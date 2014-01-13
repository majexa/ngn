<?php

class TestProjectAllErrors extends NgnTestCase {

  function test() {
    $r = (new Errors)->get();
    $this->assertFalse(!!$r, implode(', ', Arr::get($r, 'file')));
  }

}