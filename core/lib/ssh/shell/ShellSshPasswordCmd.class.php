<?php

class ShellSshPasswordCmd {

  protected $server;

  /**
   * @param array [host, pass, port]
   */
  function __construct($server) {
    $this->server = Arr::checkEmpty($server, ['host', 'pass']);
    if (!isset($this->server['user'])) $this->server['user'] = 'user';
  }

  protected function prepareCmd($cmd) {
    if (strstr($cmd, "\n")) $cmd = "<< EOF\n$cmd\nEOF";
    $port = isset($this->server['port']) ? ' -p '.$this->server['port'] : '';
    return $this->sshpass()." ssh$port -T {$this->server['user']}@{$this->server['host']} $cmd";
  }

  function sshpass() {
    return "sshpass -p '{$this->server['pass']}'";
  }

  function cmd($cmd, $output = true) {
    return sys($this->prepareCmd($cmd), $output);
  }

  function title() {
    return $this->server['user'].'@'.$this->server['host'].(isset($this->server['port']) ? ':'.$this->server['port'] : '');
  }

}