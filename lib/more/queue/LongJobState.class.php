<?php

class LongJobState {

  public $id, $states;
  protected $_id;

  function __construct($id) {
    $this->id = $id;
    if ($this->id == 'ljsample') die2('!');
    $this->_id = 'lj'.Misc::removePrefix('lj', $id);
  }

  /**
   * Устанавливает состояние задачи в позицию начала
   */
  function start() {
    $this->delete();
    $this->update('status', 'starting');
    $this->update('startTime', time());
    return $this;
  }

  function started() {
    $this->update('percentage', 0);
    $this->update('status', 'progress');
  }

  function percentage() {
    return $this->get('percentage');
  }

  function status() {
    return $this->get('status');
  }

  function get($k) {
    return Mem::get($this->_id.$k);
  }

  function data() {
    if (Mem::get($this->_id.'status') != 'complete') return false;
    return Mem::get($this->_id.'data');
  }

  function all() {
    return [
      'id' => $this->_id,
      'percentage' => $this->get('percentage'),
      'status' => $this->get('status'),
      'data' => $this->data(),
      'total' => $this->get('total'),
      'startTime' => date('d.m.Y H:i:s', $this->get('startTime')),
      'lastUpdateTrace' => $this->get('trace')
    ];
  }

  function finish($data) {
    if (!$this->status()) return;
    $this->update('status', 'complete');
    $this->update('data', $data);
  }

  function update($k, $v) {
    //if (!in_array($k, ['percentage', 'status', 'data', 'total'])) throw new Exception("No such property as '$k' for LongJob");
    Mem::set($this->_id.$k, $v);
    if ($k == 'status') {
      output("! change status {$this->id}: $v");
      Mem::set($this->id.'trace', getBacktrace(false));
    }
  }

  function delete($forceStaring = false) {
    if (!$this->status()) return;
    if (!$forceStaring and $this->status() == 'starting') throw new Exception("Can not delete '$this->id' while starting");
    output("delete {$this->id}");
    // $this->update('status', 'deleted');
    Mem::delete($this->_id.'status');
    Mem::delete($this->_id.'data');
    Mem::delete($this->_id.'percentage');
    return $this;
  }

}
