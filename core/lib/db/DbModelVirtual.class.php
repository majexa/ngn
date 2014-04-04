<?php

class DbModelVirtual extends DbModel {

  function __construct(array $r) {
    $this->r = $r;
  }

}