<?php

class LongJobCore {

  static function run(LongJobAbstract $longJob) {
    $id = $longJob->id();
    if (!Misc::validName($id)) throw new Exception("ID ($id) of longJob object (class: ".get_class($longJob).") is not valid");
    $status = self::state($id)->status();
    output("current status before adding $id: $status");
    if (!$status or $status == 'complete') {
      self::states()->start($id);
      (new ProjectQueue)->add([
        'class' => 'object',
        'object' => $longJob,
        'method' => 'cycle',
        'ljId' => $id
      ]);
      output("current status after adding $id: ".self::state($id)->status());
      return true;
    }
    output("run of $id aborted. already running");
    return false;
  }

  /**
   * @param integer Long Job ID
   * @return LongJobState
   */
  static function state($id) {
    return O::get('LongJobState', $id);
  }

  /**
   * @return LongJobStates
   */
  static function states() {
    return O::get('LongJobStates');
  }

  static function monitor() {
    $t = '';
    function replaceOut($str) {
      if (is_array($str)) $str = getPrr($str);
      $numNewLines = substr_count($str, "\n");
      echo chr(27)."[0G"; // Set cursor to first column
      echo $str;
      echo chr(27)."[".$numNewLines."A"; // Set cursor up x lines
    }
    while (true) {
      $s = "";
      foreach ((new self) as $v) {
        /* @var LongJobState $v */
        $d = $v->all();
        $s .= $d['id']." ({$d['percentage']}%): {$d['status']} {$d['total']} | ";
      }
      $s = $s ? : "                                                                                                               ";
      if ($t != $s) {
        $t = $s;
        replaceOut($s);
      }
      usleep(0.5 * 1000000);
    }
  }

}