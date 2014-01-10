<?php

class QueueWorkerInstaller extends WorkerInstaller {

  function __construct($projectName, $workersCount) {
    parent::__construct($projectName, 'queue', $workersCount);
  }

}
