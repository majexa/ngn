<?php

class ProjectQueueWorker extends QueueWorker {
  use ProjectQueueBase;

  function __construct($id) {
    $this->initProjectQueue(PROJECT_KEY);
    parent::__construct($id);
  }

}