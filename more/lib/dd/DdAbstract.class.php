<?php

abstract class DdAbstract {

  protected $strname;

  function __construct($strName) {
    Misc::checkEmpty($strName);
    $this->strName = $strName;
  }

}
