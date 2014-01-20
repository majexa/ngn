<?php

class TestWsClient extends ProjectTestCase {

  static $local = true;

  function test() {
    if (!($config = Config::getVar('wss', true))) return;
    Dir::remove(LOGS_PATH.'/wss.log');
    (new WsClient($config['host'], $config['port']))->connect()->sendData(123);
    usleep(100);
    $this->assertTrue((bool)strstr(file_get_contents(LOGS_PATH.'/wss.log'), "New message recieved: 123"));
  }

}