<?php

class AccessDenied extends Exception {

  function __construct($title = 'Доступ запрещён') {
    parent::__construct($title);
  }

}
