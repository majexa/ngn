<?php

class InotifyDaemonInstaller extends Daemon {

  protected function bin() {
    return '/usr/bin/php';
  }

  protected function script() {
    return "/home/user/ngn-env/projects/{$this->projectName}/{$this->daemonName}.php";
  }

}