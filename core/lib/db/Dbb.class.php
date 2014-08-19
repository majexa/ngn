<?php

class Dbb extends Db {

  function __construct(array $config) {
    parent::__construct($config['dbUser'], $config['dbPass'], $config['dbHost'], $config['dbName']);
  }

}