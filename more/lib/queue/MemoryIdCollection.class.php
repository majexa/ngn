<?php

class MemoryIdCollection extends ArrayAccesseble {

  protected $id;

  function __construct($id) {
    $this->id = $id;
    $this->r = Mem::get($this->id) ? : [];
    //output3('construct states. '.implode(', ', array_keys($this->r)));
  }

  function remove($id) {
    //output3("remove $id");
    //output3("before: ".implode(', ', array_keys($this->r)));
    unset($this->r[$id]);
    //output3("after: ".implode(', ', array_keys($this->r)));
    $this->store();
  }

  function destroy() {
    $this->r = [];
    Mem::delete($this->id);
  }

  function add($id) {
    //output2("adding $id");
    //output2("before: ".implode(', ', array_keys($this->r)));
    $this->r[$id] = true;
    //output2("after: ".implode(', ', array_keys($this->r)));
    $this->store();
  }

  protected function store() {
    Mem::set($this->id, $this->r);
  }

  protected function &getArrayRef() {
    $r = array_keys($this->r);
    return $r;
  }

  function getIterator() {
    return new ArrayIterator($this->getArrayRef());
  }

}
