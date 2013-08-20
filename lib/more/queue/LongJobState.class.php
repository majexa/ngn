<?php

class LongJobState {

  public $id, $states;

  function __construct($id) {
    $this->id = 'lj'.Misc::removePrefix('lj', $id);
    $this->states = new LongJobStates;
  }

  function start() {
    //$this->storeJob();
    //$status = $this->status();
    //if ($status == 'progress' or $status == 'complete') return false;
    Mem::set($this->id.'percentage', 0);
    Mem::set($this->id.'status', 'progress');
    return true;
  }

  function percentage() {
    return Mem::get($this->id.'percentage');
  }

  function status() {
    return Mem::get($this->id.'status');
  }

  function data() {
    if (Mem::get($this->id.'status') != 'complete') return false;
    return Mem::get($this->id.'data');
  }

  function all() {
    if (!($status = $this->status())) throw new NotFoundException("job ID={$this->id}");
    return [
      'percentage' => Mem::get($this->id.'percentage'),
      'status' => Mem::get($this->id.'status'),
      'data' => $this->data($this->id.'data')
    ];
  }

  function finish($data) {
    $this->update('status', 'complete');
    $this->update('data', $data);
  }

  protected function update($k, $v) {
    if (!in_array($k, ['percentage', 'status', 'data'])) {
      throw new Exception("No such property as '$k' for LongJob");
    }
    Mem::set($this->id.$k, $v);
  }

  function delete() {
    $this->removeStoredJob();
    Mem::delete($this->id.'status');
    Mem::delete($this->id.'data');
    Mem::delete($this->id.'percentage');
  }

  protected function storeJob() {
    $this->jobs[] = [
      'id' => $this->id,
      'backtrace' => getBacktrace(false)
    ];
    $this->storeJobs();
  }

  protected function storeJobs() {
    Mem::set('longJobs', $this->jobs);
  }

  protected function removeStoredJob() {
    foreach ($this->jobs as $n => $v) if ($v['id'] == $this->id) unset($this->jobs[$n]);
    $this->jobs = array_values($this->jobs);
    $this->storeJobs();
  }

  static function removeStoredJobs() {
    Mem::delete('longJobs');
  }

  static function storedJobs() {
    return Mem::get('longJobs') ?: [];
  }

}
