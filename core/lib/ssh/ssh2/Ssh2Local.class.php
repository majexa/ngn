<?php

class Ssh2Local implements Ssh2ShellInterface {

  function exec($cmd) {
    if (is_array($cmd)) $cmd = implode('; ', $cmd);
    return Cli::shell($cmd);
  }

  // here it's alias
  function shell(array $cmd) {
    $this->exec($cmd);
  }

}