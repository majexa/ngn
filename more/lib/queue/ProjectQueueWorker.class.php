<?php

class ProjectQueueWorker extends QueueWorker {

  function __construct($id) {
    $this->setName(PROJECT_KEY);
    parent::__construct($id);
  }

}