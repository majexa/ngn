<?php

trait ObjectPropertyGetter {

  public $p = [];

  function __get($k) {
    if (isset($this->p[$k])) {

      if ($k == 'tree' and !$this->p[$k] and DdTags::isTree($this->p['fieldType'])) throw new Exception('!');

      return $this->p[$k];
    }
    else throw new Exception("Property '$k' not exists");
  }

  function p() {
    return $this->p;
  }

  function __isset($k) {
    return isset($this->p[$k]);
  }

}