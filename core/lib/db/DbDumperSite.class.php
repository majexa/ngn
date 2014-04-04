<?php

class DbDumperSite extends DbDumper {
  
  function __construct() {
    parent::__construct(db());
  }
  
  function exportOnlyDd($flag) {
    $this->includeRule = $flag ? 'dd_i_.*' : '';
  }

  function exportDdItemsTables() {
  }

}