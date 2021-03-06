<?php

class QueueWorker extends QueueBase {

  protected $id;

  function __construct($id) {
    parent::__construct();
    $this->id = $id;
    Dir::make(DATA_PATH.'/queue');
  }

  function run() {
    $this->getExchange();
    $this->output("Worker $this->id started");
    $this->getQueue()->consume(function (AMQPEnvelope $envelope) {
      $this->processData($envelope->getBody());
    }, AMQP_AUTOACK);
  }

  protected function processData($body) {
    $this->output("Worker $this->id start processing data");
    $t = getMicrotime();
    db()->disconnect();
    $this->_processData($body);
    db()->disconnect();
    $this->output("Worker $this->id finish processing data. Time: ".Misc::price(getMicrotime() - $t));
  }

  protected function _processData($body) {
    if (empty($body)) throw new Exception('Body is empty');
    if ($this->isDebug()) LogWriter::v('processBody', $body);
    $data = json_decode($body, true);
    if ($this->isDebug()) LogWriter::v('processData', $data);
    if ($data['class'] == 'object') {
      $o = unserialize($data['object']);
      if (isset($data['ljId']) and !is_subclass_of($o, 'LongJobAbstract')) throw new Exception('Object with class "'.get_class($o).'" must be subclass of "LongJobCycle"');
      if (isset($data['ljId'])) if ($this->isDebug()) $this->output("status: {$data['ljId']}: ".LongJobCore::state($data['ljId'])->status());
      $r = $o->{$data['method']}();
      $this->output("$this->id finished processing {$data['ljId']}. By ".($r ? 'complete' : 'abort'));
    }
    else {
      $class = ucfirst($data['class']);
      if ($data['method'] == '__construct') {
        $this->output("creating instance of $class with args: ".implode($data['data']));
        (new ReflectionClass($class))->newInstanceArgs($data['data']);
      }
      else {
        if (isset($data['data'])) {
          $this->output("calling {$data['method']} method of $class with args: ".implode($data['data']));
          call_user_func_array([new $class, $data['method']], $data['data']);
        } else {
          $this->output("calling {$data['method']} method of $class");
          (new $class)->{$data['method']}();
        }
      }
    }
  }

}