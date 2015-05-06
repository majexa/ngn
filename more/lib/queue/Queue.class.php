<?php

class Queue extends QueueBase {

  /**
   * @api
   * <pre>
   * Примеры:
   *
   * {this}([
   *   'class' => 'ClassName',
   *   'data' => ['param1', 'param2', ...] // параметры конструктора
   * ]);
   *
   * {this}([
   *   'class' => 'ClassName',
   *   'method' => 'methodName',
   *   'data' => ['param1', 'param2', ...] // параметры метода
   * ]);
   *
   * {this}([
   *   'class' => 'object',
   *   'object' => $object, // объект (должен уметь сеарилизоваться)
   *   'method' => 'method' // его метод
   * ]);
   *
   * {this}([
   *   'class' => 'object',
   *   'object' => $longJobObject,
   *   'method' => 'cycle',
   *   'ljId' => 'ljSomeId'
   * ]);
   * </pre>
   *
   * @param array $data
   * @throws Exception
   */
  function add(array $data) {
    Arr::checkEmpty($data, ['class']);
    if (!isset($data['method'])) $data['method'] = '__construct';
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