<?php

class ProjectDaemonInstaller extends DaemonInstaller {

  protected function defineOptions() {
    $config = json_decode(`run site {$this->projectName} var {$this->daemonName}`, JSON_FORCE_OBJECT);
    return [
      'bin'     => '/usr/bin/php',
      'opts'    => "/home/user/ngn-env/projects/{$this->projectName}/{$this->daemonName}.php",
      'workers' => isset($config['workers']) ? $config['workers'] : 1
    ];
  }

}