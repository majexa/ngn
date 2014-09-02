<?php

abstract class DdAbstract {

  protected $strName;

  function __construct($strName) {
    Misc::checkEmpty($strName);
    $this->strName = $strName;
  }

}
