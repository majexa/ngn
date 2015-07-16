<?php

class ShellSshCmd {

  protected $server;

  /**
   * @param array $server [host, user, port]
   */
  function __construct(array $server) {
    $this->server = Arr::checkEmpty($server, ['host']);
    if (!isset($this->server['user'])) $this->server['user'] = 'user';
  }

  protected function prepareCmd($cmd) {
    if (strstr($cmd, "\n")) $cmd = "<< EOF\n$cmd\nEOF";
    $port = isset($this->server['port']) ? ' -p '.$this->server['port'] : '';
    return "ssh$port -T {$this->server['user']}@{$this->server['host']} $cmd";
  }

  function cmd($cmd, $output = true) {
    return sys($this->prepareCmd($cmd), $output);
  }

  function title() {
    return $this->server['user'].'@'.$this->server['host'].(isset($this->server['port']) ? ':'.$this->server['port'] : '');
  }

}