<?php

abstract class LongJobAbstract {
  use DebugOutput;

  public $state;
  protected $object, $n, $percentage, $total;

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
  abstract function iteration();
  abstract protected function result();

  protected function complete() {
    return $this->n >= $this->total();
  }

  function cycle() {
    set_time_limit(0);
    if (!isset($this->state)) throw new Exception('U need to call parent constructor in the end of '.get_class($this).' class constructor');
    $this->output("Start long job cycle. Class: ".get_class($this));
    $this->state->started();
    $total = $this->total();
    $this->output("items total: $total");
    if (!$total) {
      $this->state->finish(false);
      $this->output("no records");
    }
    $this->state->update('total', $total);
    $step = $this->step();
    $this->n = 0;
    while (1) {
      if (!$this->state->status()) return false; // если задача снята, выходим из цикла
      $this->percentage = round($this->n / $total * 100);
      $this->state->update('percentage', $this->percentage);
      if ($this->complete()) {
        $this->state->finish($this->result());
        $this->output("finished. status: ".LongJobCore::state($this->id())->status());
        return true;
      }
      $before = Misc::formatPrice(memory_get_usage());
      $this->iteration();
      $this->output($this->state->id.'. status='.$this->state->status().": Long Job Iteration. STEP: $step, cur: $this->n, total: $total, cur: ".($this->n + $step).', mem before: '.$before.', mem after: '.Misc::formatPrice(memory_get_usage()));
      $this->n += $step;
    }
  }

}