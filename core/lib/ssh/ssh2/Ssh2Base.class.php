<?php

class Ssh2Base {

  protected $connection;

  function __construct(Ssh2Connection $connection) {
    $this->connection = $connection();
  }

}