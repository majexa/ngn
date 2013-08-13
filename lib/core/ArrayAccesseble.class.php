<?php

abstract class ArrayAccesseble implements ArrayAccess, IteratorAggregate {

  protected function &getArrayRef() {
    return $this->r;
  }

  function getIterator() {
    return new ArrayIterator($this->getArrayRef());
  }

  function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->getArrayRef()[] = $value;
    } else {
      $this->getArrayRef()[$offset] = $value;
    }
  }
  
  function offsetExists($offset) {
    return isset($this->getArrayRef()[$offset]);
  }
  
  function offsetUnset($offset) {
    unset($this->getArrayRef()[$offset]);
  }
  
  function offsetGet($offset) {
    return isset($this->getArrayRef()[$offset]) ? $this->getArrayRef()[$offset] : null;
  }

}