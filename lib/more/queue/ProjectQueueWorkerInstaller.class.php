<?php

class ProjectQueueWorkerInstaller extends QueueWorkerInstaller {

  function __construct() {
    if (!Config::getVar('queue', true)) SiteConfig::updateSubVar('queue', 'workers', 4);
    parent::__construct(PROJECT_KEY, Config::getSubVar('queue', 'workers'));
  }

}