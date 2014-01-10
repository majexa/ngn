<?php

class ProjectQueueWorkerInstaller extends QueueWorkerInstaller {

  function __construct() {
    parent::__construct(PROJECT_KEY, Config::getSubVar('queue', 'workers'));
  }

}