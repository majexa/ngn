<?php

class ProjectQueue extends LongJobQueue {

  function __construct($projectKey = null) {
    if (!$projectKey and !defined('PROJECT_KEY')) throw new Exception("Define PROJECT_KEY constant or $projectKey");
    if (defined('PROJECT_KEY')) $projectKey = PROJECT_KEY;
    $this->exName = $projectKey;
    $this->queueName = $projectKey;
    parent::__construct();
  }

  function addDefault($method, $data) {
    $this->add([
      'class' => ucfirst($this->exName).'QueueActions',
      'method' => $method,
      'data' => $data
    ]);
  }

}