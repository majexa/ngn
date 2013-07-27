<?php

class VkAuthBase {

  public $auth;

  function __construct(VkAuth $auth) {
    Misc::checkEmpty($auth->authorized);
    $this->auth = $auth;
  }

}