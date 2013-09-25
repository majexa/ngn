<?php

class LongJobState {

  public $id, $states;

  function __construct($id, LongJobStates $states = null) {
    $this->id = 'lj'.Misc::removePrefix('lj', $id);
    $this->states = $states ?: new LongJobStates;
  }

  /**
   * Устанавливает состояние задачи в позицию начала
   */
  function start() {
    $this->abort();
    Mem::set($this->id.'percentage', 0);
    Mem::set($this->id.'status', 'progress');
    $this->states->add($this->id);
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
    //if (!($status = $this->status())) throw new NotFoundException("job ID={$this->id}");
    return [
      'id' => $this->id,
      'percentage' => Mem::get($this->id.'percentage'),
      'status' => Mem::get($this->id.'status'),
      'data' => $this->data($this->id.'data'),
      //'lastUpdateTrace' => Mem::get($this->id.'trace')
    ];
  }

  function finish($data) {
    if (!$this->status()) return;
    $this->update('status', 'complete');
    $this->update('data', $data);
  }

  function update($k, $v) {
    if (!$this->status() and $k != 'status') return;
    if (!in_array($k, ['percentage', 'status', 'data'])) throw new Exception("No such property as '$k' for LongJob");
    Mem::set($this->id.$k, $v);
    //Mem::set($this->id.'trace', getBacktrace(false));
  }

  function abort() {
    $this->states->remove($this->id);
    Mem::delete($this->id.'status');
    Mem::delete($this->id.'data');
    Mem::delete($this->id.'percentage');
  }

  /*
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
  */

}
