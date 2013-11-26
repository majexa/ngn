<?php

class QueueWorkerInstaller extends WorkerInstaller {

  function __construct($projectName) {
    parent::__construct($projectName, 'queue', 3);
  }

}
