<?php

class DaemonInstaller extends DaemonInstallerAbstract {

  function __construct($daemon) {
    parent::__construct(PROJECT_KEY, $daemon);
  }

  protected function workersCount() {
    return Config::getSubVar($this->daemon, 'workers');
  }

  function install() {
    if (!$this->workersCount()) {
      output("'$this->daemon' daemon not enabled in '{$this->projectName}' project. skipped");
      return;
    }
    parent::install();
  }

}