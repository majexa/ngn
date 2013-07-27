<?php

abstract class ArrayAccesseble implements ArrayAccess {

  protected function &getArrayLink() {
    return $this->r;
  }

  function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->getArrayLink()[] = $value;
    } else {
      $this->getArrayLink()[$offset] = $value;
    }
  }
  
  function offsetExists($offset) {
    return isset($this->getArrayLink()[$offset]);
  }
  
  function offsetUnset($offset) {
    unset($this->getArrayLink()[$offset]);
  }
  
  function offsetGet($offset) {
    return isset($this->getArrayLink()[$offset]) ? $this->getArrayLink()[$offset] : null;
  }

}