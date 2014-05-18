<?php

class ProjectDaemonInstaller extends DaemonInstaller {

  function install() {
    print Cli::shell("pm localProject updateIndex $this->projectName");
    parent::install();
  }

}