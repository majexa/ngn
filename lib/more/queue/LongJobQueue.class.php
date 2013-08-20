<?php

/**
 * Queue with LongJob support
 */
class LongJobQueue extends Queue {

  protected function _processData($data) {
    $r = parent::_processData($data);
    if (isset($data['jobId'])) {
      $state = new LongJobState($data['jobId']);
      if ($state->status()) $state->finish($r);
    }
  }

}