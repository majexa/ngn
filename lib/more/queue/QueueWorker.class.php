<?php

class QueueWorker extends QueueBase {

  protected $id;

  function __construct($id) {
    parent::__construct();
    $this->id = $id;
    $this->run();
  }

  protected function run() {
    set_time_limit(0);
    output("Worker $this->id started");
//  //LogWriter::str('worker', "worker $this->id init");
    $this->getQueue()->consume(function (AMQPEnvelope $envelope) {
      $this->processData($envelope->getBody());
    }, AMQP_AUTOACK);
  }

  function processData($body) {
    output("Worker $this->id start processing data");
    $t = getMicrotime();
    db()->disconnect();
    $this->_processData($body);
    db()->disconnect();
    output("Worker $this->id finish processing data. Time: ".Misc::price(getMicrotime() - $t));
  }

  protected function _processData($body) {
    Dir::make(DATA_PATH.'/queue');
    $id = time().'-'.rand(100, 10000);
    file_put_contents(DATA_PATH.'/queue/'.$id, $body);
    if (empty($body)) throw new Exception('Body is empty');
    $data = json_decode($body, true);
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
      if (isset($data['ljId'])) {
        //  //LogWriter::str('worker', "$this->id started processing {$data['ljId']}");
        output("status: {$data['ljId']}: ".LongJobCore::state($data['ljId'])->status());
      }
      $r = $o->{$data['method']}();
      //  //if (isset($data['ljId'])) LogWriter::str('worker', "$this->id finished processing {$data['ljId']}. By ".($r ? 'complete' : 'abort'));
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

    unlink(DATA_PATH.'/queue/'.$id);
  }

}