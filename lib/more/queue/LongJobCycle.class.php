<?php

abstract class LongJobCycle {

  protected $object, $state, $n, $percentage, $total;

  function __construct(LongJobObject $object, LongJobState $state) {
    $this->object = $object;
    $this->state = $state;
  }

  protected function total() {
    if (isset($this->total)) return $this->total;
    return $this->total = $this->_total();
  }

  abstract protected function _total();
  abstract protected function step();
  abstract protected function iteration();
  abstract protected function result();

  protected function complete() {
    return $this->n >= $this->total();
  }

  function cycle() {
    set_time_limit(0);
    $total = $this->total();
    $step = $this->step();
    $this->n = 0;
    while (1) {
      if (!$this->longJob->status()) return; // если задача снята, выходим из цикла
      $this->percentage = round($this->n / $total * 100);
      //$before = Misc::formatPrice(memory_get_usage());
      $this->iteration();
      //LogWriter::str('ddxls', "QUEUE N: {$this->queueN}, all: $total, $n, cur: ".($n + $step).', mem before: '.$before.', mem after: '.Misc::formatPrice(memory_get_usage()));
      if ($this->complete()) return $this->result();
      $this->n += $step;
    }
  }

}