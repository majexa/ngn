<?php

trait LongJobbable {

  /**
   * @var LongJobState
   */
  public $runner = false;

  public $queueN = false;

  function setPercentage($n) {
    if ($n > 100) $n = 100;
    ProjMem::set($this->runner->id.'percentage', $n);
  }

}