<?php

class A extends ArrayObject {

  public $aaa = [1, 3];

  function __construct() {
    $aaa = &$this->aaa;
    parent::__construct($aaa);
  }

}

$a = new A;
$a->append(4);
foreach ($a as $v) print "* $v\n";