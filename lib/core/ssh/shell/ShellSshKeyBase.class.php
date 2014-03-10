<?php

class ShellSshKeyBase {

  protected $cmd;

  function __construct(ShellSshPasswordCmd $cmd) {
    $this->cmd = $cmd;
    ShellSshLocal::config();
    ShellSshLocal::genKey();
  }

}