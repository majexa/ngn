<?php

class ProjectQueue extends Queue {
  use ProjectQueueBase;

  function __construct() {
    $this->initProjectQueue(PROJECT_KEY);
    parent::__construct();
  }

  function addDefault($method, $data) {
    $this->add([
      'class'  => ucfirst($this->exName).'QueueActions',
      'method' => $method,
      'data'   => $data
    ]);
  }

}