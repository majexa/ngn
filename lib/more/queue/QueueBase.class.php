<?php

class QueueBase {
  use DebugOutput;

  protected $n, $channel, $exchange, $queue, $exName = 'exchange1', $queueName = 'exchange1';

  function __construct() {
    $connection = new AMQPConnection;
    try {
      $connection->connect();
    } catch (Exception $e) {
      LogWriter::v('rabbitErr', $e->getMessage());
      (new SendEmail)->send('anges_91@mail.ru', 'RabbitMQ server connection error', $e->getMessage(),false);
      throw new Exception('RabbitMQ server connection error');
    }
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

  function getQueue() {
    if (isset($this->queue)) return $this->queue;
    $this->queue = new AMQPQueue($this->channel);
    $this->queue->setName($this->queueName);
    $this->queue->declare();
    $this->queue->bind($this->exName, 'global');
    return $this->queue;
  }

}