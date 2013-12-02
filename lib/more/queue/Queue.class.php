<?php

class Queue extends QueueBase {

  function add(array $data) {
    Arr::checkEmpty($data, ['class', 'method']);
    if ($data['class'] == 'object') {
      Arr::checkEmpty($data, 'object');
      $data['object'] = serialize($data['object']);
    }
    $attr = empty($data['id']) ? [] : ['message_id' => $data['id']];
    $this->output("Adding data. Exchange: $this->exName, queue: $this->queueName");
    $body = json_encode($data);
    if ($this->isDebug()) LogWriter::v('publishBody', $body);
       if (!($this->getExchange()->publish($body, 'global', AMQP_NOPARAM, $attr))) {
      throw new Exception('Publish data error');
    }
  }

}