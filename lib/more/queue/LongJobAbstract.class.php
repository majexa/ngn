<?php

abstract class LongJobAbstract {

  protected $object, $state, $n, $percentage, $total;

  function __construct() {
    $this->state = LongJobCore::state($this->id());
  }

  protected function total() {
    if (isset($this->total)) return $this->total;
    return $this->total = $this->_total();
  }

  /**
   * @return string Unical ID for this job
   */
  abstract function id();

  abstract protected function _total();
  abstract protected function step();
  abstract protected function iteration();
  abstract protected function result();

  protected function complete() {
    return $this->n >= $this->total();
  }

  function cycle() {
    set_time_limit(0);
    if (!isset($this->state)) throw new Exception('U need to call parent constructor in the end of '.get_class($this).' class constructor');
    $this->state->start();
    $total = $this->total();
    $step = $this->step();
    $this->n = 1;
    while (1) {
      if (!$this->state->status()) return; // если задача снята, выходим из цикла
      $this->percentage = round($this->n / $total * 100);
      $this->state->update('percentage', $this->percentage);
      $before = Misc::formatPrice(memory_get_usage());
      $this->iteration();
      output("Long Job Iteration. STEP: $step, cur: $this->n, total: $total, cur: ".($this->n + $step).', mem before: '.$before.', mem after: '.Misc::formatPrice(memory_get_usage()));
      if ($this->complete()) {
        $this->state->finish($this->result());
        output("finished. status: ".LongJobCore::state($this->id())->status());
        return;
      }
      $this->n += $step;
    }
  }

}