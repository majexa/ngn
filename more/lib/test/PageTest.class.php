<?php

class PageTest {

  protected $case, $domain, $base;

  function __construct(PHPUnit_Framework_TestCase $case, $domain) {
    $this->case = $case;
    $this->domain = $domain;
    $this->base = 'http://'.$this->domain;
  }

  protected $cache = [];

  protected function retrieve($path) {
    if (isset($this->cache[$path])) return $this->cache[$path];
    $path = $this->base.$path;
    return $this->cache[$path] = O::get('Curl')->get($path, true);
  }

  function check($path) {
    $r = $this->retrieve($path);
    $this->case->assertTrue((bool)strstr($r[0], '200 OK'), "$path is not available. Contents:\n{$r[1]}");
    $this->case->assertFalse((bool)strstr($r[1], '<b>Warning</b>'), "$path has warning:\n".$r[1]);
    $this->case->assertFalse((bool)strstr($r[1], '<b>Fatal error</b>'), "$path has fatal error:\n".$r[1]);
    return $r;
  }

  function isJson($path) {
    $r = $this->retrieve($path);
    $this->case->assertTrue(is_object(json_decode($r[1])), "no json in $path result:\n".$r[1]);
  }

}