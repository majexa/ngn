<?php

class Error404 extends NotLoggableError {

  public function __construct($message = '', $code = 0, Exception $previous = null) {
    if (!$message) $message = '404 error';
    parent::__construct($message, $code, $previous);
  }

}