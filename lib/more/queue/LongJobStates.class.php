<?php

class LongJobStates extends ArrayAccesseble implements IteratorAggregate {

  function __construct() {
    $this->r = Mem::get('longJobs');
  }

  function remove($id) {
    foreach ($this->r as $n => $v) if ($v['id'] == $id) unset($this->r[$n]);
    $this->r = array_values($this->r);
    $this->store();
  }

  function removeAll() {
    Mem::delete('longJobs');
  }

  function add($id) {
    $this->r[] = [
      'id' => $id,
      'backtrace' => getBacktrace(false)
    ];
    $this->store();
  }

  protected function store() {
    Mem::set('longJobs', $this->r);
  }

  function getIterator() {
    return new ArrayIterator($this->getArrayRef());
  }

}