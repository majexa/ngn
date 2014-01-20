<?php

class TestWsClient extends ProjectTestCase {

  static $local = true;

  function test() {
    if (!($config = Config::getVar('wss', true))) return;
    Dir::remove(LOGS_PATH.'/wss.log');
    (new WsClient($config['host'], $config['port']))->connect()->sendData(123);
    usleep(400);
    $n=0;
    while (!file_exists(LOGS_PATH.'/wss.log')) {
      if ($n == 10) $this->assertTrue(false, 'wss message not received');
      usleep(200);
      $n++;
    }
    output("Log appears. Attempt $n");
    $this->assertTrue((bool)strstr(file_get_contents(LOGS_PATH.'/wss.log'), "New message received: 123"), 'wss message not received');
  }

}