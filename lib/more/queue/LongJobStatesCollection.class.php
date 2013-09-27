<?php

class LongJobStatesIterator extends ArrayIterator {

  function current() {
    $id = parent::current();
    return new LongJobState($id);
  }

}

class LongJobStatesCollection extends MemoryIdCollection {

  function __construct() {
    parent::__construct('longJob');
  }

  function getIterator() {
    return new LongJobStatesIterator($this->getArrayRef());
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










/*




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
    output("remove($id)");
    prr($this->r);
    output("removed($id)");
    if (($k = array_search($id, $this->r)) !== false) {
      unset($this->r[$k]);
      $this->r = array_values($this->r);
    }
    prr($this->r);
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
      foreach ((new LongJobStates) as $state) {
$d = $state->all();
$s .= $d['id']." ({$d['percentage']}%): {$d['status']} {$d['total']} | ";
}
$s = $s ?: "                                                                                                               ";
if ($t != $s) {
  $t = $s;
  replaceOut($s);
}
usleep(0.5 * 1000000);
}
}

}






*/

