<?php

class NoFileException extends Exception {

  function __construct($text, $code = 777, $previous = null) {
    parent::__construct(strstr($text, ' ') ? $text : 'File or dir "'.$text.'" does not exists', $code, $previous);
  }

}
