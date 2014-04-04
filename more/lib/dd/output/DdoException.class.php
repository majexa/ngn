<?php

class DdoException extends Exception {

  protected $parentException;

  function __construct(Exception $parentException, $message) {
    $this->parentException = $parentException;
    parent::__construct($message);
  }

  function getBacktrace() {
    return array_merge($this->parentException->getTrace(), parent::getTrace());
  }

}