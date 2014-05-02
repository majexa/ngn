<?php

class InotifyDaemonInstaller extends DaemonInstallerAbstract {

  protected function bin() {
    return '/usr/bin/php';
  }

  protected function opts() {
    return "/home/user/ngn-env/projects/{$this->projectName}/{$this->daemon}.php";
  }

}