<?php

class ShellSshLocal {

  static function config() {
    $config = "StrictHostKeyChecking=no\nLogLevel=quiet\nUserKnownHostsFile=/dev/null";
    if (strstr(file_get_contents('/home/user/.ssh/config'), $config)) return;
    file_put_contents('/home/user/.ssh/config', $config);
  }

  static function genKey() {
    if (file_exists('/home/user/.ssh/id_rsa')) return;
    output(`ssh-keygen -q -f ~/.ssh/id_rsa -t rsa -N ''`);
  }

}