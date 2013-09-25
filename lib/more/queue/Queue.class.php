<?php

class Queue {

  protected $n, $channel, $exchange, $queue, $exName = 'exchange1', $queueName = 'exchange1';

  function __construct() {
    $connection = new AMQPConnection;
    $connection->connect();
    if (!$connection->isConnected()) throw new Exception('Can not connect');
    $this->channel = new AMQPChannel($connection);
    $this->getExchange();
  }

  function worker() {
    set_time_limit(0);
    output("Starting worker. Exchange: $this->exName, queue: $this->queueName");
    $o = $this;
    output('queueworker'.$this->exName.'started');
    Mem::set('queueworker'.$this->exName.'started', true);
    $this->getQueue()->consume(function(AMQPEnvelope $envelope) use ($o) {
      $o->processData($envelope->getBody());
    }, AMQP_AUTOACK);
  }

  protected function getExchange() {
    if (isset($this->exchange)) return $this->exchange;
    $this->exchange = new AMQPExchange($this->channel);
    $this->exchange->setName($this->exName);
    $this->exchange->setType('fanout');
    $this->exchange->declare();
    return $this->exchange;
  }

  function add(array $data) {
    Arr::checkEmpty($data, ['class', 'method']);
    if ($data['class'] == 'object') {
      Arr::checkEmpty($data, 'object');
      $data['object'] = serialize($data['object']);
    }
    $attr = empty($data['id']) ? [] : ['message_id' => $data['id']];
    output("Adding data. Exchange: $this->exName, queue: $this->queueName");
    if (!($this->getExchange()->publish(json_encode($data), 'global', AMQP_NOPARAM, $attr))) {
      throw new Exception('Publish data error');
    }
  }

  function getQueue() {
    if (isset($this->queue)) return $this->queue;
    $this->queue = new AMQPQueue($this->channel);
    $this->queue->setName($this->queueName);
    $this->queue->declare();
    $this->queue->bind($this->exName, 'global');
    return $this->queue;
  }

  function get($flags = 0) {
    if (!($envelope = $this->getQueue()->get($flags))) return false;
    $data = $envelope->getBody();
    $data = json_decode($data, true);
    return $data;
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
     *   'jobId' => 'ljSomeId'
     * ]
     */
    if ($data['class'] == 'object') {
      $o = unserialize($data['object']);
      if (isset($data['jobId'])) {
        if (!is_subclass_of($o, 'LongJobCycle')) {
          throw new Exception('Object with class "'.get_class($o).'" must be subclass of "LongJobCycle"');
        }
      }
      $o->{$data['method']}();
      if (isset($data['jobId'])) {
        output("status: {$data['jobId']}: ".LongJobCore::state($data['jobId'])->status());
      }
    } else {
      $class = ucfirst($data['class']);
      if ($data['method'] == '__construct') {
        new $class($data['data']);
      } else {
        (new $class)->{$data['method']}($data['data']);
      }
    }
    unlink(DATA_PATH.'/queue/'.$id);
  }

  function processData($body) {
    db()->disconnect();
    $this->_processData($body);
    db()->disconnect();
  }

  function eventName($event, $id) {
    return $this->exName.$this->queueName.$id.$event;
  }

}