<?php

class DaemonInstallerCore {

  static function install() {
    if (!($daemons = Config::getVar('daemons', true))) return;
    foreach ($daemons as $daemon) (new DaemonInstaller($daemon))->install();
  }

}