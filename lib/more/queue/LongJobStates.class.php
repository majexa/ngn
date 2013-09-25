<?php

class LongJobStatesIterator extends ArrayIterator {

  function current() {
    $id = parent::current();
    return new LongJobState($id);
  }

}

class LongJobStates extends ArrayAccesseble {

  function __construct() {
    $this->r = Mem::get('longJobs') ?: [];
  }

  function remove($id) {
    unset($this->r[array_search($id, $this->r)]);
    $this->store();
  }

  function destroy() {
    Mem::delete('longJobs');
  }

  function add($id) {
    $this->r[] = $id;
    $this->store();
  }

  protected function store() {
    Mem::set('longJobs', $this->r);
  }

  function getIterator() {
    return new LongJobStatesIterator($this->getArrayRef());
  }

  static function monitor() {
    function replaceOut($str) {
      if (is_array($str)) $str = getPrr($str);
      $numNewLines = substr_count($str, "\n");
      echo chr(27)."[0G"; // Set cursor to first column
      echo $str;
      echo chr(27)."[".$numNewLines."A"; // Set cursor up x lines
    }
    while (true) {
      $s = "";
      foreach ((new LongJobStates) as $state) {
        /* @var LongJobState $state */
        $d = $state->all();
        $s .= "{$d['id']} ({$d['percentage']}%): {$d['status']} | ";
        replaceOut($s);
      }
      usleep(50000);
    }
  }

}