<?php

class NoMethodException extends Exception {

  function __construct($method) {
    parent::__construct('Method "'.$method.'" does not exists');
  }

}