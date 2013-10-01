<?php

class Queue extends QueueBase {

  function add(array $data) {
    Arr::checkEmpty($data, ['class', 'method']);
    if ($data['class'] == 'object') {
      Arr::checkEmpty($data, 'object');
      $data['object'] = serialize($data['object']);
    }
    $attr = empty($data['id']) ? [] : ['message_id' => $data['id']];
    output("Adding data. Exchange: $this->exName, queue: $this->queueName");
    LogWriter::str('worker', "publish new data");
    $body = json_encode($data);
    LogWriter::v('publishBody', $body);
    if (!($this->getExchange()->publish($body, 'global', AMQP_NOPARAM, $attr))) {
      throw new Exception('Publish data error');
    }
  }

}