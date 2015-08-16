<?php

class NotFoundException extends Exception {

  function __construct($text) {
    parent::__construct((strstr($text, ' ') ? $text : '"'.$text.'"').' not found');
  }

}