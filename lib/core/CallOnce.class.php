<?php

trait CallOnce {

  private $called = [];

  protected function callOnce($method) {
    if (in_array($method, $this->called)) return;
    $this->$method();
    $this->called[] = $method;
  }

}