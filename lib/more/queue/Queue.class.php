<?php

class Queue {

  protected $n, $channel, $exchange, $queue, $exName = 'exchange1', $queueName = 'exchange1';

  function __construct() {
    $connection = new AMQPConnection();
    $connection->connect();
    $this->n = (ProjMem::get('queueN') ?: 0) + 1;
    ProjMem::set('queueN', $this->n);
    if (!$connection->isConnected()) throw new Exception('Can not connect');
    $this->channel = new AMQPChannel($connection);
    $this->getExchange();
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
    if (!($this->getExchange()->publish(json_encode($data), 'global', AMQP_NOPARAM, $attr))) {
      throw new Exception('=(');
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

  function worker() {
    set_time_limit(0);
    print "\nStarting worker...";
    $o = $this;
    $this->getQueue()->consume(function(AMQPEnvelope $envelope) use ($o) {
      $o->processData($envelope->getBody());
    }, AMQP_AUTOACK);
  }

  function cron() {
    set_time_limit(0);
    $d = opendir(DATA_PATH.'/queue') or die($php_errormsg);
    while (false !== ($f = readdir($d))) {
      $f = DATA_PATH.'/queue/'.$f;
      if(is_file($f)) {
        $body = file_get_contents($f);
        $data = json_decode($body, true);
        $class = ucfirst($data['class']);
        (new $class)->{$data['method']}($data['data']);
        unlink($f);
      }
    }
    closedir($d);
  }

  protected function _processData($body) {
    Dir::make(DATA_PATH.'/queue');
    $id = time().'-'.rand(100, 10000);
    file_put_contents(DATA_PATH.'/queue/'.$id, $body);
    $data = json_decode($body, true);
    if ($data['class'] == 'object') {
      $o = unserialize($data['object']);
      if (isset($data['jobId'])) {
        if (!ClassCore::hasTrait($o, 'LongJob')) throw new Exception('Object with class "'.get_class($o).'" must use trait "LongJob"');
        $o->queueN = $this->n;
      }
      $r = $o->{$data['method']}();
    } else {
      $class = ucfirst($data['class']);
      if ($data['method'] == '__construct') {
        new $class($data[2]);
        $r = null;
      } else {
        $r = (new $class)->{$data['method']}($data['data']);
      }
    }
    unlink(DATA_PATH.'/queue/'.$id);
    return $r;
  }

  function processData($body) {
    db()->disconnect();
    $r = $this->_processData($body);
    db()->disconnect();
    return $r;
  }

  function eventName($event, $id) {
    return $this->exName.$this->queueName.$id.$event;
  }

}