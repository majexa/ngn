<?php

class NoFileException extends Exception {

  function __construct($file, $code = 777, $previous = null) {
    parent::__construct('File or dir "'.$file.'" does not exists', $code, $previous);
  }

}
