<?php

class LongJobRunner {

  public $id, $job, $method, $queue;

  function __construct($id, $job = null, $method = null) {
    $this->id = PROJECT_KEY.'lj'.$id;
    $this->job = $job;
    $this->method = $method;
    if ($this->job and isset($this->job->runner)) $this->job->runner = $this;
  }

  function start() {
    Misc::checkEmpty($this->job);
    Misc::checkEmpty($this->method);
    $status = $this->status();
    if ($status == 'progress' or $status == 'complete') return false;
    ProjMem::set($this->id.'percentage', 0);
    ProjMem::set($this->id.'status', 'progress');
    /*
    //if (ClassCore::hasTrait($this->source, 'LongJob')) ProjMem::set($this->id.'percentage', 0);
    $queue = new ProjectQueue;
    Ngn::addEvent($queue->eventName('start', $this->id), function($data) {
      LogWriter::str('longJob', 'queue job started');
    });
    Ngn::addEvent($queue->eventName('complete', $this->id), function($data) {
      LogWriter::str('longJob', 'complete');
      LogWriter::str('longJob', "set status {$data['jobId']}status=complete");
      ProjMem::set($data['jobId'].'status', 'complete');
      ProjMem::set($data['jobId'].'data', $data);
    });
    */
    (new ProjectQueue)->add([
      'class' => 'object',
      'object' => $this->job,
      'method' => $this->method,
      'jobId' => $this->id
    ]);
    return true;
  }

  function percentage() {
    return self::_percentage($this->id);
  }

  function status() {
    return $this->_status($this->id);
  }

  function data() {
    return self::_data($this->id);
  }

  static function _percentage($id) {
    return ProjMem::get($id.'percentage');
  }

  static function _status($id) {
    return ProjMem::get($id.'status');
  }

  static function _data($id) {
    if (self::_status($id) != 'complete') return false;
    return ProjMem::get($id.'data');
  }

  function all() {
    return [
      'percentage' => $this->percentage(),
      'status' => $this->status(),
      'data' => $this->data()
    ];
  }

  function delete() {
    ProjMem::delete($this->id.'status');
    ProjMem::delete($this->id.'data');
    ProjMem::delete($this->id.'percentage');
  }

}
