<?php

class SmsSender {

  function send($phone, $msg) {
    $conf = Config::getVar('littleSms');
    $api = new LittleSms($conf['user'], $conf['key'], false);
    if (($balance = $api->userBalance()) < 1) throw new Exception("Low sms balance ($balance)");
    if (!$ids = $api->messageSend($phone, $msg)) throw new Exception($this->getResponse());
  }

}