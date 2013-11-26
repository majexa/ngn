<?php

class WssWorkerInstaller extends WorkerInstaller {

  function __construct($projectName) {
    parent::__construct($projectName, 'wss');
  }

}