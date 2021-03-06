<?php

class Ssh2Sftp {

  protected $sftp;

  function __construct(Ssh2Connection $connection) {
    $this->sftp = ssh2_sftp($connection->connection);
  }

  function __call($func, $args) {
    $func = "ssh2_sftp_$func";
    if (function_exists($func)) {
      array_unshift($args, $this->sftp);
      return call_user_func_array($func, $args);
    }
    else {
      throw new Exception($func.' is not a valid SFTP function');
    }
  }

  function putContents($file, $data) {
    file_put_contents('ssh2.sftp://'.$this->sftp.$file, $data);
  }

}