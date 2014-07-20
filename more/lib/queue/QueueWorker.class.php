<?php

class QueueWorker extends QueueBase {

  protected $id;

  function __construct($id) {
    parent::__construct();
    $this->id = $id;
    Dir::make(DATA_PATH.'/queue');
    $this->run();
  }

  protected function run() {
    set_time_limit(0);
    $this->output("Worker $this->id started");
    $this->getQueue()->consume(function (AMQPEnvelope $envelope) {
      $this->processData($envelope->getBody());
    }, AMQP_AUTOACK);
  }

  function processData($body) {
    $this->output("Worker $this->id start processing data");
    $t = getMicrotime();
    db()->disconnect();
    $this->_processData($body);
    db()->disconnect();
    $this->output("Worker $this->id finish processing data. Time: ".Misc::price(getMicrotime() - $t));
  }

  protected function _processData($body) {
    //$id = time().'-'.rand(100, 10000);
    //if ($this->isDebug()) file_put_contents(DATA_PATH.'/queue/'.$id, $body);
    if (empty($body)) throw new Exception('Body is empty');
    if ($this->isDebug()) LogWriter::v('processBody', $body);
    $data = json_decode($body, true);
    if ($this->isDebug()) LogWriter::v('processData', $data);
    /**
     * Примеры $data:
     * [
     *   'class' => 'className',
     *   'method' => '__construct',
     *   'data' => ['param1', 'param2', ...]
     * ]
     * [
     *   'class' => 'className',
     *   'method' => 'methodName',
     *   'data' => ['param1', 'param2', ...]
     * ]
     * [
     *   'class' => 'object',
     *   'object' => $object,
     *   'method' => 'method',
     * ]
     * [
     *   'class' => 'object',
     *   'object' => $longJobObject,
     *   'method' => 'cycle',
     *   'ljId' => 'ljSomeId'
     * ]
     */
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
        new $class($data['data']);
      }
      else {
        (new $class)->{$data['method']}($data['data']);
      }
    }
    //unlink(DATA_PATH.'/queue/'.$id);
  }

  protected function isDebug() {
    return false;
  }

}