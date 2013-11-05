<?php

trait ProjectQueueBase {

  protected function initProjectQueue($projectKey) {
    $this->exName = $projectKey;
    $this->queueName = $projectKey;
  }

}
