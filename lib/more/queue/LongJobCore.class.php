<?php

class LongJobCore {

  static function run(LongJobAbstract $longJob) {
    $id = 'lj'.$longJob->id();
    $status = self::state($id)->status();
    output("current status before adding: $status");
    if (!$status or $status == 'complete') {
      (new ProjectQueue('kp'))->add([
        'class' => 'object',
        'object' => $longJob,
        'method' => 'cycle',
        'jobId' => $id
      ]);
      self::state($id)->start();
      return true;
    }
    return false;
  }

  /**
   * @param integer Long Job ID
   * @return LongJobState
   */
  static function state($id) {
    return O::get('LongJobState', $id);
  }

}