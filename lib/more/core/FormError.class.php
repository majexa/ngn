<?php

class FormError extends NgnValidError {

  public $elementName;
  
  function __construct($elementName, $message, $code = 123, $previous = null) {
    $this->elementName = $elementName;
    parent::__construct($message, $code, $previous);
  }

}