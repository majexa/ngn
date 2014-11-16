<?php

class ProjectDaemonInstaller extends DaemonInstaller {

  protected function defineOptions() {
    $config = FileVar::getVar("/home/user/ngn-env/project/{$this->projectName}/config/vars/{$this->daemonName}.php");
    return [
      'bin'     => '/usr/bin/php',
      'opts'    => "/home/user/ngn-env/project/{$this->projectName}/{$this->daemonName}.php",
      'workers' => isset($config['workers']) ? $config['workers'] : 1
    ];
  }

}