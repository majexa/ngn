<?php

class Ssh2DefaultConnection extends Ssh2PublicKeyConnection {

  function __construct($host, $username = 'user', $port = 22) {
    parent::__construct($host, $port, $username, $_SERVER['HOME'].'/.ssh/id_rsa.pub', $_SERVER['HOME'].'/.ssh/id_rsa');
  }

}