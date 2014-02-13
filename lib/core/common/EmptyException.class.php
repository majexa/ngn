<?php

class EmptyException extends Exception {

  function __construct($str, $code = 666, $previous = null) {
    parent::__construct(strstr($str, ' ') ? $str : '"'.$str.'" can not be empty', $code, $previous);
  }

}
