<?php

class NotFoundException extends Exception {

  function __construct($text) {
    parent::__construct('"'.$text.'" not found');
  }

}